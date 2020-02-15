<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Trait ConfirmedByManagerTrait.
 */
trait ConfirmedByManagerTrait
{
    /**
     * Confirmed by manager.
     *
     * @var DateTimeInterface|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $confirmedByManager = null;

    public function isConfirmedByManager(): bool
    {
        return $this->confirmedByManager ? true : false;
    }

    public function getConfirmedByManager(): ?DateTimeInterface
    {
        return $this->confirmedByManager;
    }

    public function setConfirmedByManager(?DateTimeInterface $confirmedByUser): void
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
