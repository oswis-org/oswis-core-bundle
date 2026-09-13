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
}
