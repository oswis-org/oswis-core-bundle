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
trait ActivatedTrait
{
    /**
     * Active after.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $activated = null;

    public function isActivated(?DateTime $dateTime = null): bool
    {
        return null !== $this->getActivated() && $this->getActivated() <= ($dateTime ?? new DateTime());
    }

    public function getActivated(): ?DateTime
    {
        return $this->activated;
    }

    public function setActivated(?DateTime $activated): void
    {
        $this->activated = $activated;
    }

    public function activate(?DateTime $dateTime = null): void
    {
        $this->setActivated($this->getActivated() ?? $dateTime ?? new DateTime());
    }
}
