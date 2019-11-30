<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

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
    final public function isLockedForUserChanges(): bool
    {
        return $this->lockedForUserChanges ? new DateTime() > $this->lockedForUserChanges : false;
    }

    final public function getLockedForUserChanges(): ?DateTime
    {
        return $this->lockedForUserChanges;
    }

    final public function setLockedForUserChanges(?DateTime $lockedForUserChanges): void
    {
        $this->lockedForUserChanges = $lockedForUserChanges;
    }

    /**
     * @throws Exception
     */
    final public function lockForUserChanges(): void
    {
        if (!$this->lockedForUserChanges) {
            $this->lockedForUserChanges = new DateTime();
        }
    }

    /**
     * @throws Exception
     */
    final public function unlockForUserChanges(): void
    {
        $this->lockedForUserChanges = null;
    }
}
