<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use Exception;

trait LockedForUserChangesTrait
{
    /**
     * Locked for user changes (edge datetime).
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $lockedForUserChanges = null;

    /**
     * @throws Exception
     */
    public function isLockedForUserChanges(): bool
    {
        return $this->lockedForUserChanges ? new DateTime() > $this->lockedForUserChanges : false;
    }

    public function getLockedForUserChanges(): ?DateTime
    {
        return $this->lockedForUserChanges;
    }

    public function setLockedForUserChanges(?DateTime $lockedForUserChanges): void
    {
        $this->lockedForUserChanges = $lockedForUserChanges;
    }

    /**
     * @throws Exception
     */
    public function lockForUserChanges(): void
    {
        if (!$this->lockedForUserChanges) {
            $this->lockedForUserChanges = new DateTime();
        }
    }

    /**
     * @throws Exception
     */
    public function unlockForUserChanges(): void
    {
        $this->lockedForUserChanges = null;
    }
}
