<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait ColorTrait
{

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $color;

    /**
     * @return string|null
     */
    final public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    final public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    final public function isForegroundWhite(): bool
    {
        if (strlen($this->color) === 4) {
            [$r, $g, $b] = sscanf($this->color, '#%1x%1x%1x');
        } else {
            [$r, $g, $b] = sscanf($this->color, '#%2x%2x%2x');
        }

        return ($r * 0.299 + $g * 0.587 + $b * 0.114) > 186;
    }
}
