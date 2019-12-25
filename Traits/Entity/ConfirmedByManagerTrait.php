<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;

/**
 * Trait ConfirmedByManagerTrait.
 */
trait ConfirmedByManagerTrait
{
    /**
     * Confirmed by manager.
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $confirmedByManager = null;

    public function isConfirmedByManager(): bool
    {
        return $this->confirmedByManager ? true : false;
    }

    public function getConfirmedByManager(): ?DateTime
    {
        return $this->confirmedByManager;
    }

    public function setConfirmedByManager(?DateTime $confirmedByUser): void
    {
        $this->confirmedByManager = $confirmedByUser;
    }

    /**
     * @throws Exception
     */
    public function confirmByManager(): void
    {
        if (!$this->confirmedByManager) {
            $this->confirmedByManager = new DateTime();
        }
    }
}
