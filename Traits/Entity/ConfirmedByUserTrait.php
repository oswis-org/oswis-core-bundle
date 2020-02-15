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
 * Trait ConfirmedByUserTrait.
 */
trait ConfirmedByUserTrait
{
    /**
     * Confirmed by user.
     *
     * @var DateTimeInterface|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $confirmedByUser = null;

    public function isConfirmedByUser(): bool
    {
        return $this->confirmedByUser ? true : false;
    }

    public function getConfirmedByUser(): ?DateTimeInterface
    {
        return $this->confirmedByUser;
    }

    public function setConfirmedByUser(?DateTimeInterface $confirmedByUser): void
    {
        $this->confirmedByUser = $confirmedByUser;
    }

    /**
     * @throws Exception
     */
    public function confirmByUser(): void
    {
        if (!$this->confirmedByUser) {
            $this->confirmedByUser = new DateTime();
        }
    }
}
