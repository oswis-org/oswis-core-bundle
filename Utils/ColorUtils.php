<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Utils;

/**
 * Práce s barvami — hlavně volba čitelné barvy textu na pozadí, které přichází z DAT.
 *
 * PROČ TO EXISTUJE: barvu příznaku, kategorie, pásku nebo role si volí tým v adminu, takže
 * šablona dopředu neneví, jestli bude tmavá nebo světlá. Napsat `color:#fff` natvrdo znamená,
 * že na každé světlejší barvě je text nečitelný.
 *
 * JEDINÝ ZDROJ PRAVDY: do 2026-08-15 na tuhle jednu otázku odpovídala tři různá místa —
 * tahle třída (YIQ, práh 186), `ProgramExtension::contrastColor()` (YIQ, práh 150) a natvrdo
 * napsaná bílá v šablonách. Dvě z těch tří odpovědí byly u středních tónů špatně. Teď počítá
 * barvu jen tahle třída a ostatní se na ni odkazují:
 *  - {@see \OswisOrg\OswisCoreBundle\Traits\Common\ColorTrait::getForegroundColor()} pro entity,
 *  - {@see \OswisOrg\OswisCoreBundle\Twig\Extension\ColorExtension} (filtr `contrast_color`)
 *    pro šablony, kde barva pozadí není z entity.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class ColorUtils
{
    /** Světlá varianta textu. */
    final public const string TEXT_LIGHT = '#ffffff';

    /** Tmavá varianta. Ne čistá černá — na barvě působí měkčeji a kontrast to prakticky nemění. */
    final public const string TEXT_DARK = '#111111';

    /**
     * Vrátí barvu textu, která je na daném pozadí čitelnější — světlou, nebo tmavou.
     *
     * Rozhoduje SKUTEČNÝ kontrastní poměr podle WCAG 2.x, ne starší YIQ práh. Ten u středních
     * tónů volil špatně a v paletě UP se spletl u 4 z 11 barev; pokaždé sáhl po bílé tam, kde
     * výsledek propadl pod AA (4,5:1) — např. `#3AB0E1` dostalo 2,48:1 místo možných 7,62:1.
     *
     * Prázdný nebo nesmyslný vstup → světlá varianta (zachovává dřívější chování).
     */
    final public static function contrastTextColor(?string $background): string
    {
        $normalized = self::normalizeHex($background);
        if (null === $normalized) {
            return self::TEXT_LIGHT;
        }

        return self::contrastRatio(self::TEXT_LIGHT, $normalized) >= self::contrastRatio(self::TEXT_DARK, $normalized)
            ? self::TEXT_LIGHT
            : self::TEXT_DARK;
    }

    /**
     * Je na téhle barvě čitelnější BÍLÝ text?
     *
     * Ponecháno kvůli zpětné kompatibilitě (veřejné API core bundlu). Nové kódy volají rovnou
     * {@see contrastTextColor()} — vrací přímo použitelnou barvu a nenutí volajícího si ji
     * dopisovat, což byl přesně ten způsob, jak vzniklo víc nesouhlasných implementací.
     */
    final public static function isOppositeWhite(?string $color = null): bool
    {
        return self::TEXT_LIGHT === self::contrastTextColor($color);
    }

    /**
     * Kontrastní poměr dvou barev podle WCAG 2.x — od 1:1 (shodné) po 21:1 (černá vs. bílá).
     * AA požaduje 4,5:1 pro běžný text, 3:1 pro velký.
     *
     * Vrací 1.0, pokud některá barva nejde přečíst — nesmyslný vstup se nemá tvářit jako
     * dobrý kontrast.
     */
    final public static function contrastRatio(?string $one, ?string $other): float
    {
        $first = self::normalizeHex($one);
        $second = self::normalizeHex($other);
        if (null === $first || null === $second) {
            return 1.0;
        }
        $a = self::relativeLuminance($first);
        $b = self::relativeLuminance($second);
        [$lighter, $darker] = $a >= $b ? [$a, $b] : [$b, $a];

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /** Šestimístný hex bez mřížky, nebo null když to hex není. Zkrácený zápis `#abc` se rozvine. */
    private static function normalizeHex(?string $color): ?string
    {
        if (null === $color) {
            return null;
        }
        $value = ltrim(trim($color), '#');
        if (3 === strlen($value)) {
            $value = $value[0].$value[0].$value[1].$value[1].$value[2].$value[2];
        }

        return 6 === strlen($value) && ctype_xdigit($value) ? strtolower($value) : null;
    }

    /**
     * Relativní jas podle WCAG. Není to prostý průměr složek — každý kanál se nejdřív
     * linearizuje (gama korekce) a teprve pak váží podle citlivosti oka; právě tenhle krok
     * starší YIQ vzorec vynechává, a proto se u středních tónů mýlí.
     */
    private static function relativeLuminance(string $sixDigitHex): float
    {
        $channels = [];
        foreach ([0, 2, 4] as $offset) {
            $value = hexdec(substr($sixDigitHex, $offset, 2)) / 255;
            $channels[] = $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }
}
