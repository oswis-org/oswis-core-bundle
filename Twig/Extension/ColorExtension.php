<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use OswisOrg\OswisCoreBundle\Utils\ColorUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Barvy ve výstupech — hlavně volba čitelné barvy textu na pozadí, které přichází z DAT.
 *
 * Proč to nemůže být natvrdo: barvu kategorie příznaku, skupiny nebo role si volí tým v adminu,
 * takže šablona dopředu neví, jestli bude tmavá nebo světlá. Napsat `color:#fff` znamená, že na
 * každé světlejší barvě je štítek nečitelný (naměřeno v prohlížeči: limetka `#B2C918` s bílým
 * textem = 1,86:1, oranžová PřF `#EB6D25` = 3,11:1 — obojí hluboko pod WCAG AA 4,5:1).
 *
 * Bydlí v CORE schválně: barvy nejsou nic kalendářového ani programového a stejný problém má
 * každý bundle, který kreslí barevný štítek. Dřív to byl privátní pomocník v
 * `OswisCalendarBundle\Twig\Extension\ProgramExtension` a používalo ho jediné místo.
 */
final class ColorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('contrast_color', $this->contrastColor(...)),
        ];
    }

    /**
     * Vrátí barvu textu čitelnou na daném pozadí — tmavou, nebo světlou.
     *
     * Výpočet sám nedělá: je v {@see ColorUtils::contrastTextColor()}, aby existoval jen na
     * jednom místě. Tenhle filtr je jen cesta k němu ze šablony pro případy, kdy barva pozadí
     * nepřichází z entity (a nelze tedy použít `foregroundColor`), ale je to holý řetězec.
     */
    public function contrastColor(mixed $hex): string
    {
        return ColorUtils::contrastTextColor(is_string($hex) ? $hex : null);
    }
}
