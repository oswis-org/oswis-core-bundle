<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

use function strlen;

trait ColorTrait
{
    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $color = null;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getForegroundColor(): string
    {
        return $this->isForegroundWhite() ? '#ffffff' : '#000000';
    }

    public function isForegroundWhite(): bool
    {
        if (4 === strlen($this->color)) {
            [$r, $g, $b] = sscanf($this->color, '#%1x%1x%1x');
        } else {
            [$r, $g, $b] = sscanf($this->color, '#%2x%2x%2x');
        }

        return ($r * 0.299 + $g * 0.587 + $b * 0.114) > 186;
    }
}
