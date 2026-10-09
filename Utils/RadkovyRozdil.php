<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Utils;

/**
 * Rozdíl dvou textů po řádcích (historie verzí šablon, dávka 4) — nejdelší společná podposloupnost. Společný začátek
 * a konec se oddělí předem, takže běžná úprava pár řádků je rychlá i u dlouhé šablony. Nad {@see MAX_BUNEK} porovnání
 * (obří přepis) se rozdíl nehledá — vrátí se „vše staré pryč, vše nové přidáno", což je pravda, jen hrubší.
 */
final class RadkovyRozdil
{
    public const string STEJNE = 'stejne';
    public const string PRIDANO = 'pridano';
    public const string ODEBRANO = 'odebrano';

    private const int MAX_BUNEK = 2_000_000;

    /** @return list<array{typ: string, radek: string}> */
    public static function porovnat(?string $stary, ?string $novy): array
    {
        $a = self::radky($stary);
        $b = self::radky($novy);
        $zacatek = 0;
        while ($zacatek < count($a) && $zacatek < count($b) && $a[$zacatek] === $b[$zacatek]) {
            ++$zacatek;
        }
        $konec = 0;
        while ($konec < count($a) - $zacatek && $konec < count($b) - $zacatek && $a[count($a) - 1 - $konec] === $b[count($b) - 1 - $konec]) {
            ++$konec;
        }
        $vysledek = [];
        for ($i = 0; $i < $zacatek; ++$i) {
            $vysledek[] = ['typ' => self::STEJNE, 'radek' => $a[$i]];
        }
        $stred = self::lcs(array_slice($a, $zacatek, count($a) - $zacatek - $konec), array_slice($b, $zacatek, count($b) - $zacatek - $konec));
        array_push($vysledek, ...$stred);
        for ($i = count($a) - $konec; $i < count($a); ++$i) {
            $vysledek[] = ['typ' => self::STEJNE, 'radek' => $a[$i]];
        }

        return $vysledek;
    }

    /** Liší se texty? */
    public static function lisiSe(?string $stary, ?string $novy): bool
    {
        return self::radky($stary) !== self::radky($novy);
    }

    /**
     * @param list<string> $a
     * @param list<string> $b
     *
     * @return list<array{typ: string, radek: string}>
     */
    private static function lcs(array $a, array $b): array
    {
        $n = count($a);
        $m = count($b);
        if (0 === $n || 0 === $m || $n * $m > self::MAX_BUNEK) {
            return [
                ...array_map(static fn (string $r): array => ['typ' => self::ODEBRANO, 'radek' => $r], $a),
                ...array_map(static fn (string $r): array => ['typ' => self::PRIDANO, 'radek' => $r], $b),
            ];
        }
        // delka[i][j] = LCS přípon a[i..], b[j..]
        $delka = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));
        for ($i = $n - 1; $i >= 0; --$i) {
            for ($j = $m - 1; $j >= 0; --$j) {
                $delka[$i][$j] = $a[$i] === $b[$j] ? $delka[$i + 1][$j + 1] + 1 : max($delka[$i + 1][$j], $delka[$i][$j + 1]);
            }
        }
        $vysledek = [];
        $i = 0;
        $j = 0;
        while ($i < $n && $j < $m) {
            if ($a[$i] === $b[$j]) {
                $vysledek[] = ['typ' => self::STEJNE, 'radek' => $a[$i]];
                ++$i;
                ++$j;
            } elseif ($delka[$i + 1][$j] >= $delka[$i][$j + 1]) {
                $vysledek[] = ['typ' => self::ODEBRANO, 'radek' => $a[$i++]];
            } else {
                $vysledek[] = ['typ' => self::PRIDANO, 'radek' => $b[$j++]];
            }
        }
        for (; $i < $n; ++$i) {
            $vysledek[] = ['typ' => self::ODEBRANO, 'radek' => $a[$i]];
        }
        for (; $j < $m; ++$j) {
            $vysledek[] = ['typ' => self::PRIDANO, 'radek' => $b[$j]];
        }

        return $vysledek;
    }

    /** @return list<string> */
    private static function radky(?string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text ?? '');

        return '' === $text ? [] : explode("\n", rtrim($text, "\n"));
    }
}
