<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;

/**
 * Trait adds deleted dateTime field.
 */
trait DeletedTrait
{
    /**
     * Date and time of delete (null if not deleted).
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $deletedAt = null;

    public function getDeletedDaysAgo(): ?int
    {
        return $this->deletedAt ? $this->deletedAt->diff(new DateTime())->d : null;
    }

    public function delete(?DateTime $dateTime = null): void
    {
        $dateTime = $this->getDeletedAt() ?? $dateTime ?? new DateTime();
        $this->setDeletedAt($dateTime);
        error_log("Deleted ".$this->getId()." at ".$dateTime->format("Y-m-d H:i:s").", ".$this->getDeletedAt()->format("Y-m-d H:i:s")." (".get_class($this).")");
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
