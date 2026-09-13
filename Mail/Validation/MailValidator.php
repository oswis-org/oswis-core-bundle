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
 *   překlep = prázdné místo), shodné chyby sloučené; totéž pro uloženou šablonu (kampaň, blok);
 * - výraz `{{ … }}` mimo podmínky, který je u části příjemců prázdný = varování;
 * - neznámé bloky; co by čištění zahodilo; odkazy (http = varování, nepovolený tvar = chyba);
 *   obrázek bez popisu;
 * - MJML `strict` pro každou odlišnou skladbu bloků (ne pro každého příjemce — 0,5 s na vykreslení)
 *   + žádné `<mj-` ve výsledku (validátor MJML obsah mj-text nehlídá).
 */
final class MailValidator
{
    /**
     * Jména, která existují vždy: Twig (`_self`, `loop`…) a Symfony Mailer — ten při odeslání přidá
     * `email` (WrappedTemplatedEmail, např. `email.image()` pro vložené obrázky).
     *
     * @var list<string>
     */
    private const array SPECIAL_NAMES = ['_self', '_context', '_charset', 'loop', 'email'];

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
        ['assigned' => $assigned, 'unknown' => $unknown] = $this->collectVariables([$subjectModule, $bodyModule], $recipients[0]['context'] ?? []);
        $structures = [];
        // Předmět a text zvlášť — chyba v předmětu nesmí zakrýt chybu v textu.
        $renderBody = function (array $context) use ($body, &$structures): void {
            /** @var array<string, mixed> $context */
            $mjml = $this->renderer->renderBody($body, $context, false);
            $structures[self::structureOf($mjml)] ??= $mjml;
        };
        $reported = $this->renderEach(['V předmětu' => $this->subjectRenderer($subject), 'V textu' => $renderBody], $recipients, $result);
        self::warnUnknownVariables($unknown, $reported, $result);
        $this->checkEmptyForSomeRecipients($subject."\n".$body, $recipients, $assigned, $result);
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

    /**
     * Uložená šablona (kampaň, blok) před uložením — zdroj ještě není v DB.
     *
     * @param list<array{label: string, context: array<string, mixed>}> $recipients
     */
    public function validateTemplateSource(string $source, array $recipients, ?string $subject = null): MailValidationResult
    {
        $result = new MailValidationResult();
        $modules = [$this->parse('šablona', $source, $result)];
        if (null !== $subject) {
            $modules[] = $this->parse('předmět', $subject, $result);
        }
        if (in_array(null, $modules, true)) {
            return $result;
        }
        /** @var list<ModuleNode> $modules */
        $unknown = $this->collectVariables($modules, $recipients[0]['context'] ?? [])['unknown'];
        $parts = ['V šabloně' => fn (array $context): string => $this->twig->createTemplate($source)->render($context)];
        if (null !== $subject) {
            $parts['V předmětu'] = $this->subjectRenderer($subject);
        }
        self::warnUnknownVariables($unknown, $this->renderEach($parts, $recipients, $result), $result);

        return $result;
    }

    /**
     * Uložená šablona podle jména (slug v DB nebo soubor) — před zařazením hromadného mailu.
     *
     * @param list<array{label: string, context: array<string, mixed>}> $recipients
     */
    public function validateTemplate(string $name, array $recipients, ?string $subject = null): MailValidationResult
    {
        try {
            $source = $this->twig->getLoader()->getSourceContext($name)->getCode();
        } catch (TwigError) {
            $result = new MailValidationResult();
            $result->error(sprintf('Šablona „%s" neexistuje.', $name));

            return $result;
        }

        return $this->validateTemplateSource($source, $recipients, $subject);
    }

    /** @return \Closure(array<string, mixed>): string */
    private function subjectRenderer(string $subject): \Closure
    {
        return function (array $context) use ($subject): string {
            /** @var array<string, mixed> $context */
            return $this->renderer->renderSubject($subject, $context);
        };
    }

    private function parse(string $part, string $source, MailValidationResult $result): ?ModuleNode
    {
        try {
            return $this->twig->parse($this->twig->tokenize(new Source($source, 'mail-'.$part)));
        } catch (SyntaxError $exception) {
            $result->error(sprintf('Chyba v zápisu (%s, řádek %d): %s', $part, $exception->getTemplateLine(), self::czech($exception->getRawMessage())));

            return null;
        }
    }

    /**
     * Proměnné ze stromu šablony: nastavené v textu (`{% set %}`, cyklus) a neznámé (mimo katalog,
     * kontext, globální proměnné).
     *
     * @param list<ModuleNode>     $modules
     * @param array<string, mixed> $context
     *
     * @return array{assigned: list<string>, unknown: list<string>}
     */
    private function collectVariables(array $modules, array $context): array
    {
        $used = [];
        $assigned = [];
        foreach ($modules as $module) {
            self::collectNames($module, $used, $assigned);
        }
        $known = array_merge(
            $this->catalog->rootNames(),
            array_map(strval(...), array_keys($context)),
            array_keys($this->twig->getGlobals()),
            self::SPECIAL_NAMES,
            $assigned,
        );
        return [
            'assigned' => array_values(array_unique($assigned)),
            'unknown'  => array_values(array_diff(array_unique($used), $known)),
        ];
    }

    /**
     * Proměnná mimo nabídku = varování — jen když ji už nehlásí chyba vykreslení („Neznámá proměnná"),
     * tedy u zápisu, který prázdnotu jistí (`|default`, `is defined`) a překlep by jinak zůstal skrytý.
     *
     * @param list<string> $unknown
     * @param list<string> $reported
     */
    private static function warnUnknownVariables(array $unknown, array $reported, MailValidationResult $result): void
    {
        foreach ($unknown as $name) {
            $asError = sprintf('Neznámá proměnná „%s"', $name);
            if ([] === array_filter($reported, static fn (string $message): bool => str_contains($message, $asError))) {
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
     * Vykreslí části zprávy pro každého příjemce se striktními proměnnými. Chyba jedné části nezastaví
     * ostatní; shodné chyby u více příjemců se sloučí („U 12 příjemců (např. …)").
     *
     * @param array<string, \Closure(array<string, mixed>): mixed>      $parts      popisek části → vykreslení
     * @param list<array{label: string, context: array<string, mixed>}> $recipients
     *
     * @return list<string> nahlášené chyby bez popisku příjemce (k vyloučení duplicit)
     */
    private function renderEach(array $parts, array $recipients, MailValidationResult $result): array
    {
        $failures = [];
        $this->withStrictVariables(true, static function () use ($parts, $recipients, &$failures): void {
            foreach ($recipients as $recipient) {
                foreach ($parts as $part => $render) {
                    try {
                        $render($recipient['context']);
                    } catch (\Throwable $exception) {
                        $failures[$part.': '.self::message($exception)][] = $recipient['label'];
                    }
                }
            }
        });
        foreach ($failures as $message => $labels) {
            $result->error(1 === count($labels)
                ? sprintf('%s: %s', $labels[0], $message)
                : sprintf('U %d příjemců (např. %s): %s', count($labels), $labels[0], $message));
        }

        return array_map(strval(...), array_keys($failures));
    }

    /**
     * Výrazy `{{ … }}` mimo podmínky a cykly, které jsou u části příjemců prázdné (spec §3.5) — typicky
     * údaj, který část lidí nevyplnila. Nehlásí se výrazy, které prázdné být smějí (katalog), ani
     * proměnné nastavené v textu; výraz, který nejde vykreslit, je hlášený jinde.
     *
     * @param list<array{label: string, context: array<string, mixed>}> $recipients
     * @param list<string>                                              $assigned
     */
    private function checkEmptyForSomeRecipients(string $source, array $recipients, array $assigned, MailValidationResult $result): void
    {
        if ([] === $recipients) {
            return;
        }
        foreach (self::topLevelPrintExpressions($source) as $expression) {
            $root = 1 === preg_match('/^[A-Za-z_]\w*/', $expression, $match) ? $match[0] : '';
            if (in_array($root, $assigned, true) || $this->catalog->mayBeEmpty($expression)) {
                continue;
            }
            $source = '{{ ('.$expression.') }}';
            $empty = [];
            try {
                // Překládá se až uvnitř přepnutí: Twig striktní kontrolu proměnných zapéká do kódu.
                $this->withStrictVariables(false, function () use ($source, $recipients, &$empty): void {
                    $template = $this->twig->createTemplate($source);
                    foreach ($recipients as $recipient) {
                        // Značka (blok, obrázek) není prázdno — jen opravdu nic nebo mezery.
                        if ('' !== trim($template->render($recipient['context']))) {
                            continue;
                        }
                        // Prázdné jen proto, že výraz nejde vyhodnotit (překlep)? To je chyba hlášená
                        // výš — varování by bylo jen šumem navíc (výjimka = výraz přeskočit).
                        $this->withStrictVariables(true, fn (): string => $this->twig->createTemplate($source)->render($recipient['context']));
                        $empty[] = $recipient['label'];
                    }
                });
            } catch (\Throwable) {
                continue;
            }
            if ([] === $empty) {
                continue;
            }
            $total = count($recipients);
            $result->warning(match (true) {
                1 === $total        => sprintf('„{{ %s }}" je u příjemce prázdné.', $expression),
                count($empty) === $total => sprintf('„{{ %s }}" je prázdné u všech příjemců.', $expression),
                default             => sprintf('„{{ %s }}" je prázdné u %d z %d příjemců (např. %s).', $expression, count($empty), $total, $empty[0]),
            });
        }
    }

    /**
     * `{{ výraz }}` na nejvyšší úrovni — ne uvnitř `{% if %}`, `{% for %}` apod., kde prázdnota
     * bývá záměr. Stačí jednoduché procházení značek: jde o varování, ne o překlad.
     *
     * @return list<string>
     */
    private static function topLevelPrintExpressions(string $source): array
    {
        $nesting = ['if', 'for', 'with', 'macro', 'embed', 'verbatim'];
        preg_match_all('/\{#.*?#\}|\{%-?\s*(\w+)(.*?)-?%\}|\{\{-?(.*?)-?\}\}/s', $source, $matches, PREG_SET_ORDER);
        $depth = 0;
        $expressions = [];
        foreach ($matches as $match) {
            $tag = $match[1] ?? '';
            if ('' !== $tag) {
                // `{% set x %}…{% endset %}` (bez `=`) obaluje obsah jako podmínka; `{% set x = … %}` ne.
                if (in_array($tag, $nesting, true) || ('set' === $tag && !str_contains($match[2] ?? '', '='))) {
                    ++$depth;
                } elseif (str_starts_with($tag, 'end') && in_array(substr($tag, 3), [...$nesting, 'set'], true)) {
                    $depth = max(0, $depth - 1);
                }
                continue;
            }
            $expression = trim($match[3] ?? '');
            if (0 === $depth && '' !== $expression && !in_array($expression, $expressions, true)) {
                $expressions[] = $expression;
            }
        }

        return $expressions;
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

        return sprintf('%s (řádek %d)', self::czech($exception->getRawMessage()), $exception->getTemplateLine());
    }

    private static function czech(string $raw): string
    {
        // Pořadí je důležité: obecnější vzory až za konkrétními. {1}, {2} = zachycené části hlášky.
        $translations = [
            '/^Variable "([^"]+)" does not exist\.?$/'                                  => 'Neznámá proměnná „{1}"',
            '/^Neither the property "([^"]+)" nor one of the methods .*$/'                => 'Neznámá vlastnost nebo metoda „{1}"',
            '/^Unknown "([^"]+)" function\..*$/s'                                        => 'Neznámá funkce „{1}"',
            '/^Unknown "([^"]+)" filter\..*$/s'                                          => 'Neznámý filtr „{1}"',
            '/^Unknown "(end\w+)" tag\..*$/s'                                           => 'Přebývá „{% {1} %}" — k ní chybí začátek bloku',
            '/^Unknown "([^"]+)" tag\..*$/s'                                             => 'Neznámá značka „{% {1} %}"',
            '/^Unexpected "(\w+)" tag \(expecting closing tag for the "(\w+)" tag.*$/s' => 'Značka „{% {1} %}" místo ukončení bloku „{2}"',
            '/^Unexpected end of template\.?$/'                                         => 'Text končí uprostřed bloku — chybí jeho ukončení (např. {% endif %} nebo {% endfor %})',
            '/^Unexpected token "end of statement block".*$/'                           => 'Ve značce {% … %} chybí podmínka nebo výraz',
            '/^Unexpected token "[^"]+" of value "([^"]*)".*$/'                         => 'Tady nečekané „{1}" — zkontroluj zápis výrazu',
            '/^Unexpected "(.)"\.?$/'                                                   => 'Nečekaný znak „{1}" (chybí druhá závorka?)',
            '/^Unclosed "variable"\.?$/'                                                => 'Neuzavřená proměnná — chybí „}}"',
            '/^Unclosed "(.+)"\.?$/'                                                    => 'Neuzavřené „{1}" — chybí jeho druhá polovina',
            '/^A block must start with a tag name\.?$/'                                 => 'Značka {% %} musí začínat názvem (if, for, set…)',
            '/^Impossible to access an attribute \("([^"]+)"\) on a null variable\.?$/' => 'Nejde přečíst „{1}" — hodnota před ní je prázdná',
            '/.*Unable to generate a URL for the named route "([^"]+)".*/s'              => 'Neexistující adresa (routa) „{1}"',
        ];
        foreach ($translations as $pattern => $czech) {
            if (1 === preg_match($pattern, $raw, $match)) {
                return strtr($czech, ['{1}' => $match[1] ?? '', '{2}' => $match[2] ?? '']);
            }
        }

        return $raw;
    }
}
