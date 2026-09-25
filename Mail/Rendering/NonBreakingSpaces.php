<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

/**
 * České nedělitelné mezery v HTML mailu — doplní se samy při odeslání i v náhledu (25. 9. 2026).
 *
 * PROČ: v kampaních se psaly ručně (`&nbsp;`), a to nedůsledně — po jednopísmenném slově chyběly ve 42 %
 * případů (229 z 545), v textu z editoru prakticky vždy (editor je psát neuměl). Tady se doplní jedním
 * místem pro všechny maily; co už nedělitelné je, zůstane.
 *
 * Pravidla (jen v TEXTU mezi značkami — značky, atributy a adresy odkazů se nemění; obsah `style`,
 * `script`, `pre`, `code` a `textarea` se přeskočí):
 * - po jednopísmenné předložce či spojce: k, s, v, z, o, u, a, i (i velkým);
 * - mezi číslem a jednotkou (Kč, %, km, kg, min…), v tisících („2 800") a v datu („20. 9. 2026");
 * - po „č." před číslem.
 */
final class NonBreakingSpaces
{
    private const string NBSP = "\u{00A0}";

    /** Jednotky, od kterých se číslo neodtrhne. */
    private const string UNITS = 'Kč|%|‰|€|EUR|km|m|cm|mm|kg|g|l|ml|min|h|hod\.|s|°C|dní|dnů|dny|let|osob|lidí|ks';

    public static function apply(string $html): string
    {
        $parts = preg_split('/(<!--.*?-->|<[^>]*>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (false === $parts) {
            return $html;
        }
        $skip = null;
        foreach ($parts as $index => $part) {
            if (1 === $index % 2) {
                // Značka: hlídat, jestli nejsme uvnitř prvku, kam se nesahá (styly, skripty, kód).
                if (null === $skip && 1 === preg_match('/^<(style|script|pre|code|textarea)\b/i', $part, $match)) {
                    $skip = strtolower($match[1]);
                } elseif (null !== $skip && 1 === preg_match('/^<\/'.$skip.'\s*>/i', $part)) {
                    $skip = null;
                }
                continue;
            }
            if (null === $skip && '' !== trim($part)) {
                $parts[$index] = self::text($part);
            }
        }

        return implode('', $parts);
    }

    /** Pravidla na jeden úsek textu (HTML entity v něm zůstávají, jak jsou). */
    public static function text(string $text): string
    {
        $text = str_replace('&nbsp;', self::NBSP, $text);
        $rules = [
            // Jednopísmenná předložka/spojka; smyčka kvůli řetězení („a v lese", „k a z").
            '/(?<![\p{L}\p{N}&_])([kvszouaiKVSZOUAI]) (?=\S)/u' => '$1'.self::NBSP,
            '/(\d) (?=(?:'.self::UNITS.')(?![\p{L}]))/u' => '$1'.self::NBSP,
            '/(\d) (?=\d{3}(?!\d))/u' => '$1'.self::NBSP,
            '/(?<!\d)(\d{1,2}\.) (?=\d{1,2}\.)/u' => '$1'.self::NBSP,
            '/(?<!\d)(\d{1,2}\.) (?=\d{4}(?!\d))/u' => '$1'.self::NBSP,
            '/(?<![\p{L}])(č\.) (?=\d)/u' => '$1'.self::NBSP,
        ];
        foreach ($rules as $pattern => $replacement) {
            do {
                $before = $text;
                $text = (string) preg_replace($pattern, $replacement, $text);
            } while ($text !== $before);
        }

        return str_replace(self::NBSP, '&nbsp;', $text);
    }
}
