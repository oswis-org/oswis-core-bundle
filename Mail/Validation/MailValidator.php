<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Validation;

use OswisOrg\OswisCoreBundle\Mail\Block\MailBlockRegistry;
use OswisOrg\OswisCoreBundle\Mail\Catalog\MailCatalog;
use OswisOrg\OswisCoreBundle\Mail\Markup\MailMarkupPolicy;
use OswisOrg\OswisCoreBundle\Mail\Rendering\MailBlockRenderer;
use OswisOrg\OswisCoreBundle\Mail\Rendering\MailRenderer;
use OswisOrg\OswisCoreBundle\Twig\Extension\MailBlockExtension;
use OswisOrg\OswisCoreBundle\Twig\Extension\MjmlExtension;
use Twig\Environment;
use Twig\Error\Error as TwigError;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Expression\Variable\AssignContextVariable;
use Twig\Node\Expression\Variable\ContextVariable;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Source;

/**
 * Kontrola mailu při uložení a před odesláním (spec 2026-09-13 §3.5).
 *
 * - syntaxe Twigu; proměnná mimo katalog = varování;
 * - vykreslení se STRIKTNÍMI proměnnými pro každého příjemce (produkce je jinak benevolentní —
 *   překlep = prázdné místo), shodné chyby sloučené;
 * - neznámé bloky; co by čištění zahodilo; odkazy (http = varování, nepovolený tvar = chyba);
 *   obrázek bez popisu;
 * - MJML `strict` pro každou odlišnou skladbu bloků (ne pro každého příjemce — 0,5 s na vykreslení)
 *   + žádné `<mj-` ve výsledku (validátor MJML obsah mj-text nehlídá).
 */
final class MailValidator
{
    /** @var list<string> */
    private const array SPECIAL_NAMES = ['_self', '_context', '_charset', 'loop'];

    public function __construct(
        private readonly Environment $twig,
        private readonly MailRenderer $renderer,
        private readonly MailBlockRegistry $blocks,
        private readonly MailCatalog $catalog,
        private readonly MjmlExtension $mjml,
    ) {
    }

    /** @param list<array{label: string, context: array<string, mixed>}> $recipients */
    public function validateMessage(string $subject, string $body, array $recipients): MailValidationResult
    {
        $result = new MailValidationResult();
        $subjectModule = $this->parse('předmět', $subject, $result);
        $bodyModule = $this->parse('text', $body, $result);
        if (null === $subjectModule || null === $bodyModule) {
            return $result;
        }
        $this->checkUnknownVariables([$subjectModule, $bodyModule], $recipients[0]['context'] ?? [], $result);
        [$structures, $reported] = $this->renderForRecipients($subject, $body, $recipients, $result);
        // Značky, odkazy a bloky se kontrolují vždy — i když některá proměnná nejde vyhodnotit (ta už
        // je hlášená výš). Benevolentní vykreslení = neznámá proměnná je prázdné místo; když nejde ani
        // to (např. neexistující routa), kontroluje se aspoň zdroj.
        $html = $body;
        if ([] !== $recipients) {
            $context = $recipients[0]['context'];
            try {
                [$html, $mjml] = $this->withStrictVariables(false, fn (): array => [
                    $this->renderer->renderTwig($body, $context),
                    $this->renderer->renderBody($body, $context, false),
                ]);
                // Skladba bloků i tehdy, když striktní průchod žádnou nevrátil (MJML se pak zkontroluje).
                $structures[self::structureOf($mjml)] ??= $mjml;
            } catch (\Throwable $exception) {
                // Striktní průchod ji mohl zakrýt dřívější neznámou proměnnou — nahlásit, pokud ještě není.
                $message = 'V textu: '.self::message($exception);
                if (!in_array($message, $reported, true)) {
                    $result->error(sprintf('%s: %s', $recipients[0]['label'], $message));
                }
            }
        }
        $this->checkBlocks(array_merge(self::staticBlockKeys($bodyModule), MailBlockRenderer::keysIn($html)), $result);
        $this->checkMarkup($html, $result);
        foreach ($structures as $mjml) {
            $this->checkMjml($mjml, $result);
        }

        return $result;
    }

    /** @param list<array{label: string, context: array<string, mixed>}> $recipients */
    public function validateTemplateSource(string $source, array $recipients): MailValidationResult
    {
        $result = new MailValidationResult();
        if (null === $this->parse('šablona', $source, $result)) {
            return $result;
        }
        foreach ($recipients as $recipient) {
            try {
                $this->twig->createTemplate($source)->render($recipient['context']);
            } catch (\Throwable $exception) {
                $result->error(sprintf('%s: %s', $recipient['label'], self::message($exception)));
            }
        }

        return $result;
    }

    private function parse(string $part, string $source, MailValidationResult $result): ?ModuleNode
    {
        try {
            return $this->twig->parse($this->twig->tokenize(new Source($source, 'mail-'.$part)));
        } catch (SyntaxError $exception) {
            $result->error(sprintf('Chyba v zápisu (%s, řádek %d): %s', $part, $exception->getTemplateLine(), $exception->getRawMessage()));

            return null;
        }
    }

    /**
     * @param list<ModuleNode>     $modules
     * @param array<string, mixed> $context
     */
    private function checkUnknownVariables(array $modules, array $context, MailValidationResult $result): void
    {
        $used = [];
        $assigned = [];
        foreach ($modules as $module) {
            self::collectNames($module, $used, $assigned);
        }
        $known = array_merge($this->catalog->rootNames(), array_keys($context), self::SPECIAL_NAMES, $assigned);
        foreach (array_unique($used) as $name) {
            if (!in_array($name, $known, true)) {
                $result->warning(sprintf('Proměnná „%s" není v nabídce „Vložit" — zkontroluj, že je napsaná správně.', $name));
            }
        }
    }

    /**
     * @param list<string> $used
     * @param list<string> $assigned
     */
    private static function collectNames(Node $node, array &$used, array &$assigned): void
    {
        $name = $node->hasAttribute('name') ? $node->getAttribute('name') : null;
        if (is_string($name)) {
            if ($node instanceof AssignContextVariable) {
                $assigned[] = $name;
            } elseif ($node instanceof ContextVariable) {
                $used[] = $name;
            }
        }
        foreach ($node as $child) {
            self::collectNames($child, $used, $assigned);
        }
    }

    /**
     * Klíče bloků zapsané přímo ve zdroji — `{{ blok('klíč') }}`. Kontrolují se i tehdy, když
     * text nejde vykreslit (vykreslený zástupný prvek by pak chyběl).
     *
     * @return list<string>
     */
    private static function staticBlockKeys(Node $node): array
    {
        $keys = [];
        if ($node instanceof FunctionExpression && MailBlockExtension::FUNCTION_NAME === $node->getAttribute('name')) {
            foreach ($node->getNode('arguments') as $argument) {
                if ($argument instanceof ConstantExpression && is_string($argument->getAttribute('value'))) {
                    $keys[] = $argument->getAttribute('value');
                }
                break;
            }
        }
        foreach ($node as $child) {
            $keys = array_merge($keys, self::staticBlockKeys($child));
        }

        return $keys;
    }

    /**
     * @param list<array{label: string, context: array<string, mixed>}> $recipients
     *
     * @return array{0: array<string, string>, 1: list<string>} skladba bloků → MJML fragment; nahlášené chyby
     */
    private function renderForRecipients(string $subject, string $body, array $recipients, MailValidationResult $result): array
    {
        $structures = [];
        $failures = [];
        $this->withStrictVariables(true, function () use ($subject, $body, $recipients, &$structures, &$failures): void {
            foreach ($recipients as $recipient) {
                // Předmět a text zvlášť — chyba v předmětu nesmí zakrýt chybu v textu.
                try {
                    $this->renderer->renderSubject($subject, $recipient['context']);
                } catch (\Throwable $exception) {
                    $failures['V předmětu: '.self::message($exception)][] = $recipient['label'];
                }
                try {
                    $mjml = $this->renderer->renderBody($body, $recipient['context'], false);
                    $structures[self::structureOf($mjml)] ??= $mjml;
                } catch (\Throwable $exception) {
                    $failures['V textu: '.self::message($exception)][] = $recipient['label'];
                }
            }
        });
        foreach ($failures as $message => $labels) {
            $result->error(1 === count($labels)
                ? sprintf('%s: %s', $labels[0], $message)
                : sprintf('U %d příjemců (např. %s): %s', count($labels), $labels[0], $message));
        }

        return [$structures, array_map(strval(...), array_keys($failures))];
    }

    /** @param list<string> $keys */
    private function checkBlocks(array $keys, MailValidationResult $result): void
    {
        foreach (array_unique($keys) as $key) {
            if (null === $this->blocks->get($key)) {
                $result->error(sprintf('Neznámý vložený blok „%s".', $key));
            }
        }
    }

    private function checkMarkup(string $html, MailValidationResult $result): void
    {
        $allowed = MailMarkupPolicy::allowedElements();
        $document = \Dom\HTMLDocument::createFromString('<!DOCTYPE html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
        $body = $document->body;
        if (null === $body) {
            return;
        }
        $removed = [];
        foreach ($body->getElementsByTagName('*') as $element) {
            $name = strtolower($element->localName);
            if (!array_key_exists($name, $allowed)) {
                $removed[] = in_array($name, MailMarkupPolicy::DROPPED_ELEMENTS, true)
                    ? sprintf('<%s> i s obsahem', $name)
                    : sprintf('značka <%s> (text zůstane)', $name);
                continue;
            }
            foreach ($element->attributes as $attribute) {
                $attributeName = strtolower($attribute->localName);
                if (!in_array($attributeName, $allowed[$name], true)) {
                    $removed[] = sprintf('atribut %s u <%s>', $attributeName, $name);
                } elseif ('style' === $attributeName) {
                    foreach (MailMarkupPolicy::splitStyle($attribute->value)['removed'] as $property) {
                        $removed[] = sprintf('styl „%s" u <%s>', $property, $name);
                    }
                } elseif ('class' === $attributeName) {
                    foreach (MailMarkupPolicy::splitClasses($attribute->value)['removed'] as $class) {
                        $removed[] = sprintf('třída „%s" u <%s>', $class, $name);
                    }
                }
            }
            foreach (['href' => MailMarkupPolicy::LINK_SCHEMES, 'src' => MailMarkupPolicy::MEDIA_SCHEMES] as $attr => $schemes) {
                $url = $element->getAttribute($attr);
                // `{{ … }}` v adrese = zdroj, který nešel vykreslit — adresa se ověří až po opravě chyby.
                if (null === $url || '' === $url || str_contains($url, '{{') || !in_array($attr, $allowed[$name], true)) {
                    continue;
                }
                $scheme = parse_url($url, PHP_URL_SCHEME);
                $scheme = is_string($scheme) ? strtolower($scheme) : '';
                if ('http' === $scheme) {
                    $result->warning(sprintf('Odkaz „%s" není zabezpečený (http) — použij https.', $url));
                } elseif (!in_array($scheme, $schemes, true)) {
                    $result->error(sprintf('Adresa „%s" má nepovolený tvar (musí začínat https://, u odkazu i mailto: nebo tel:) — při odeslání by se odstranila.', $url));
                }
            }
            if ('mj-image' === $name && '' === trim((string) $element->getAttribute('alt'))) {
                $result->error('Obrázek nemá popis (alt) — bez něj ho neuvidí lidé se čtečkou ani ti, kdo mají obrázky vypnuté.');
            }
        }
        foreach (array_unique($removed) as $what) {
            $result->warning(sprintf('Při odeslání se odstraní: %s.', $what));
        }
    }

    private function checkMjml(string $fragment, MailValidationResult $result): void
    {
        $document = '<mjml><mj-body><mj-section><mj-column>'.$fragment.'</mj-column></mj-section></mj-body></mjml>';
        foreach ($this->mjml->validate($document) as $message) {
            $result->error('MJML: '.$message);
        }
        if (str_contains($this->mjml->mjmlToHtml($document), '<mj-')) {
            $result->error('Tlačítko, obrázek nebo oddělovač je uvnitř odstavce nebo seznamu — v mailu by nefungoval. Dej ho samostatně mezi odstavce.');
        }
    }

    private static function structureOf(string $mjml): string
    {
        preg_match_all('~<(mj-[a-z-]+)~', $mjml, $matches);

        return implode(',', $matches[1]);
    }

    /**
     * Vykreslení s dočasně zapnutými / vypnutými striktními proměnnými (na produkci je Twig
     * benevolentní, v testech striktní — kontrola se musí chovat stejně všude).
     *
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
     */
    private function withStrictVariables(bool $strict, callable $callback): mixed
    {
        $was = $this->twig->isStrictVariables();
        $strict ? $this->twig->enableStrictVariables() : $this->twig->disableStrictVariables();
        try {
            return $callback();
        } finally {
            $was ? $this->twig->enableStrictVariables() : $this->twig->disableStrictVariables();
        }
    }

    /** Česká hláška pro člověka — nejčastější chyby Twigu přeložené, ostatní beze změny. */
    private static function message(\Throwable $exception): string
    {
        if (!$exception instanceof TwigError) {
            return $exception->getMessage();
        }
        $raw = $exception->getRawMessage();
        $translations = [
            '/^Variable "([^"]+)" does not exist\.?$/'                                    => 'Neznámá proměnná „%s"',
            '/^Neither the property "([^"]+)" nor one of the methods .*$/'                  => 'Neznámá vlastnost nebo metoda „%s"',
            '/^Unknown "([^"]+)" function\.?.*$/'                                         => 'Neznámá funkce „%s"',
            '/^Unknown "([^"]+)" filter\.?.*$/'                                           => 'Neznámý filtr „%s"',
            '/^Impossible to access an attribute \("([^"]+)"\) on a null variable\.?$/'   => 'Nejde přečíst „%s" — hodnota před ní je prázdná',
            '/.*Unable to generate a URL for the named route "([^"]+)".*/s'                => 'Neexistující adresa (routa) „%s"',
        ];
        foreach ($translations as $pattern => $czech) {
            if (1 === preg_match($pattern, $raw, $match)) {
                $raw = sprintf($czech, $match[1]);
                break;
            }
        }

        return sprintf('%s (řádek %d)', $raw, $exception->getTemplateLine());
    }
}
