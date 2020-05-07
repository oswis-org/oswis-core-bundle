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
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
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
