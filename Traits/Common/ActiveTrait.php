<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;

/**
 * Trait adds "active" field.
 */
trait ActiveTrait
{
    /**
     * Active after.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $active = null;

    public function isActive(?DateTime $dateTime = null): bool
    {
        return null !== $this->getActive() && $this->getActive() >= ($dateTime ?? new DateTime());
    }

    public function getActive(): ?DateTime
    {
        return $this->active;
    }

    public function setActive(?DateTime $active): void
    {
        $this->active = $active;
    }

    public function activate(?DateTime $dateTime = null): void
    {
        $this->setActive($this->getActive() ?? $dateTime ?? new DateTime());
    }
}
