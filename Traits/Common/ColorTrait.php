<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Utils\ColorUtils;

trait ColorTrait
{
    /** Color (hexadecimal HTML notation). */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', nullable: true)]
    protected ?string $color = null;

    /**
     * Barva textu čitelná na {@see getColor()} — počítá {@see ColorUtils::contrastTextColor()},
     * aby na tuhle otázku existovala v celém systému jediná odpověď (dřív byly tři a dvě
     * u středních tónů vracely nečitelnou kombinaci).
     */
    public function getForegroundColor(): string
    {
        return ColorUtils::contrastTextColor($this->getColor());
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
