<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

/**
 * `<mj-image … />` → `<mj-image …></mj-image>`. HTML parser (prohlížeč i `\Dom\HTMLDocument`
 * sanitizéru) samouzavírací zápis u neznámých značek ignoruje a do značky „spolkne" všechen
 * následující text (ověřeno 13. 9. 2026). Musí proběhnout PŘED čímkoli, co HTML parsuje.
 */
final class MjmlTagNormalizer
{
    public static function normalize(string $html): string
    {
        return (string) preg_replace('~<(mj-[a-z-]+)(\s[^<>]*?)?\s*/>~i', '<$1$2></$1>', $html);
    }
}
