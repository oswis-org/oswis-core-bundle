<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Markup;

use OswisOrg\OswisCoreBundle\Mail\Rendering\MailBlockRenderer;

/**
 * JEDINÁ definice HTML v textu mailu (spec 2026-09-13 §4.3; plán dávky 1, odchylka 2).
 *
 * - {@see self::EDITOR_ELEMENTS}: slovník editoru (co umí vytvořit WYSIWYG);
 * - {@see self::allowedElements()}: navíc bezpečné HTML, které smí zůstat jako „chráněný kód"
 *   z režimu kódu (tabulky…) — jinak by se při odeslání ztratilo.
 * Čte ji sanitizér, kontrola chyb a od dávky 2 i editor → nemůžou se rozejít.
 */
final class MailMarkupPolicy
{
    /** Delší tělo se odmítne s hláškou (výchozí limit sanitizéru 20 000 B tělo potichu ořezával). */
    public const int MAX_BODY_BYTES = 500_000;

    /** Hodnota CSS, která neprojde nikdy (načítání zdrojů, skripty, únik z atributu). */
    private const string UNSAFE_CSS_VALUE = '/url\s*\(|expression\s*\(|javascript:|[<>"\\\\]/i';

    /** @var list<string> */
    public const array LINK_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    /** @var list<string> */
    public const array MEDIA_SCHEMES = ['https', 'http'];

    /**
     * Třídy s významem v `mj-style` šablony `message.html.twig`.
     *
     * @var list<string>
     */
    public const array CLASSES = ['warning', 'highlight', 'token-box'];

    /** @var list<string> */
    public const array CSS_PROPERTIES = [
        'text-align', 'color', 'font-weight', 'font-style', 'text-decoration', 'width', 'vertical-align',
        'padding', 'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
        'margin', 'margin-top', 'margin-bottom',
    ];

    /**
     * Slovník editoru: prvek → povolené atributy.
     *
     * @var array<string, list<string>>
     */
    public const array EDITOR_ELEMENTS = [
        'p' => ['style'], 'h2' => ['style'], 'h3' => ['style'],
        'strong' => ['class'], 'em' => [], 'u' => [], 'small' => [], 'br' => [], 'span' => ['class'],
        'ul' => [], 'ol' => [], 'li' => [],
        'a' => ['href', 'title', 'target', 'class'],
        'mj-button' => ['href', 'title'], 'mj-image' => ['src', 'alt', 'width', 'href'], 'mj-divider' => [],
    ];

    /**
     * Zahodit i s obsahem (text uvnitř nesmí zůstat).
     *
     * @var list<string>
     */
    public const array DROPPED_ELEMENTS = [
        'script', 'style', 'head', 'title', 'meta', 'link', 'template', 'noscript',
        'iframe', 'object', 'embed', 'svg', 'math', 'form', 'input', 'button', 'select', 'textarea',
    ];

    /**
     * Bezpečné HTML navíc pro chráněný kód.
     *
     * @var array<string, list<string>>
     */
    private const array PROTECTED_ELEMENTS = [
        'b' => ['class'], 'i' => [], 'h4' => ['style'], 'div' => ['style', 'class'], 'hr' => [], 'blockquote' => [],
        'table' => ['style', 'class', 'width'], 'thead' => [], 'tbody' => [], 'tr' => ['style'],
        'td' => ['style', 'colspan', 'rowspan', 'width', 'align'], 'th' => ['style', 'colspan', 'rowspan', 'width', 'align'],
        'img' => ['src', 'alt', 'width', 'height', 'style'],
    ];

    /** @return array<string, list<string>> */
    public static function allowedElements(): array
    {
        return self::EDITOR_ELEMENTS + self::PROTECTED_ELEMENTS + [MailBlockRenderer::PLACEHOLDER_ELEMENT => ['data-key']];
    }

    public static function isAllowedClass(string $class): bool
    {
        return in_array($class, self::CLASSES, true);
    }

    public static function isAllowedCssProperty(string $property): bool
    {
        return in_array(strtolower(trim($property)), self::CSS_PROPERTIES, true);
    }

    /**
     * Rozdělí `style` na deklarace, které čištěním projdou, a deklarace, které zahodí (tak, jak je
     * autor napsal — kvůli radě ke zápisu) — jedno místo pro čištění i pro kontrolu („při odeslání se
     * odstraní…"). Hodnota s url(), expression(), javascript: nebo znaky < > " \ neprojde nikdy.
     *
     * @return array{kept: list<string>, removed: list<string>}
     */
    public static function splitStyle(string $style): array
    {
        $kept = [];
        $removed = [];
        foreach (explode(';', $style) as $declaration) {
            [$property, $value] = array_pad(explode(':', $declaration, 2), 2, '');
            $property = strtolower(trim($property));
            $value = trim($value);
            if ('' === $property && '' === $value) {
                continue;
            }
            if ('' === $value || !self::isAllowedCssProperty($property) || 1 === preg_match(self::UNSAFE_CSS_VALUE, $value)) {
                $removed[] = trim($declaration);
                continue;
            }
            $kept[] = $property.': '.$value;
        }

        return ['kept' => $kept, 'removed' => $removed];
    }

    /**
     * Rada k deklaraci stylu, kterou čištění zahodí — hlavně pro nejčastější překlep v zarovnání
     * (`align=justify`, `text-align=center` místo `text-align: justify`; nalezeno 13. 9. 2026
     * v prvním hromadném mailu po nasazení). Null = rada není (nepovolená vlastnost).
     */
    public static function styleHint(string $declaration): ?string
    {
        if (1 === preg_match('/^(?:text-)?align\s*[=:]?\s*["\']?(left|right|center|justify)\b/i', trim($declaration), $match)) {
            return sprintf('správně style="text-align: %s"', strtolower($match[1]));
        }
        if (!str_contains($declaration, ':')) {
            return 'styl se píše „vlastnost: hodnota", např. style="text-align: justify"';
        }

        return null;
    }

    /** @return array{kept: list<string>, removed: list<string>} */
    public static function splitClasses(string $classes): array
    {
        $kept = [];
        $removed = [];
        foreach (preg_split('/\s+/', trim($classes), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $class) {
            if (self::isAllowedClass($class)) {
                $kept[] = $class;
            } else {
                $removed[] = $class;
            }
        }

        return ['kept' => $kept, 'removed' => $removed];
    }
}
