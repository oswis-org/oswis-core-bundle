<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use Exception;

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
    protected ?DateTime $deleted = null;

    public function getDeletedDaysAgo(): ?int
    {
        return $this->deleted ? $this->deleted->diff(new DateTime())->d : null;
    }

    /**
     * @throws Exception
     */
    public function delete(?DateTime $dateTime = null): void
    {
        $this->setDeleted($this->getDeleted() ?? $dateTime ?? new DateTime());
    }

    /**
     * Get date and time when entity was deleted.
     */
    public function getDeleted(): ?DateTime
    {
        return $this->deleted;
    }

    public function setDeleted(?DateTime $deleted = null): void
    {
        if (null === $deleted || (null === $this->deleted && null !== $deleted)) {
            $this->setEMailDeleteConfirmationDateTime(null);
        }
        $this->deleted = $deleted;
    }

    public function isDeleted(?DateTime $referenceDateTime = null): bool
    {
        try {
            return $this->deleted && ($referenceDateTime ?? new DateTime()) > $this->deleted;
        } catch (Exception $e) {
            return false;
        }
    }
}
