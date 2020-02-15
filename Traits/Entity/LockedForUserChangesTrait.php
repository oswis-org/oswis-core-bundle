<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use Exception;

trait LockedForUserChangesTrait
{
    /**
     * Locked for user changes (edge datetime).
     *
     * @var DateTimeInterface|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $lockedForUserChanges = null;

    /**
     * @throws Exception
     */
    public function isLockedForUserChanges(): bool
    {
        return $this->lockedForUserChanges ? new DateTime() > $this->lockedForUserChanges : false;
    }

    public function getLockedForUserChanges(): ?DateTimeInterface
    {
        return $this->lockedForUserChanges;
    }

    public function setLockedForUserChanges(?DateTimeInterface $lockedForUserChanges): void
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
