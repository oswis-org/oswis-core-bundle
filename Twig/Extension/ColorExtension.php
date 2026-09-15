<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use OswisOrg\OswisCoreBundle\Utils\ColorUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Colour helpers for templates — mainly a readable text colour for a background colour that comes from data
 * (colours of categories, groups or roles are chosen in the admin, so a fixed text colour is unreadable on light
 * backgrounds). Generic for every bundle that renders coloured labels.
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
