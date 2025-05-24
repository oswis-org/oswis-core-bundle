<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds deleted dateTime field.
 */
trait DeletedTrait
{
    /** Date and time of delete (null if not deleted). */
    #[Column(type: 'datetime', nullable: true, options: ['default' => null])]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?DateTime $deletedAt = null;

    public function getDeletedDaysAgo(): ?int
    {
        return $this->deletedAt?->diff(new DateTime())->d;
    }

    public function delete(?DateTime $dateTime = null): void
    {
        $dateTime = $this->getDeletedAt() ?? $dateTime ?? new DateTime();
        $this->setDeletedAt($dateTime);
    }

    /**
     * Get date and time when entity was deleted.
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt = null): void
    {
        if (null === $deletedAt || null === $this->deletedAt) {
            $this->deletedAt = $deletedAt;

            return;
        }
        $this->deletedAt = $deletedAt > $this->deletedAt ? $this->deletedAt : $deletedAt;
    }

    public function isDeleted(?DateTime $referenceDateTime = null): bool
    {
        return $this->deletedAt && ($referenceDateTime ?? new DateTime()) > $this->deletedAt;
    }
}
