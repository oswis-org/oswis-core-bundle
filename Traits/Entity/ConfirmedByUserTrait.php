<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;

/**
 * Trait ConfirmedByUserTrait.
 */
trait ConfirmedByUserTrait
{
    /**
     * Confirmed by user.
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $confirmedByUser = null;

    final public function isConfirmedByUser(): bool
    {
        return $this->confirmedByUser ? true : false;
    }

    final public function getConfirmedByUser(): ?DateTime
    {
        return $this->confirmedByUser;
    }

    final public function setConfirmedByUser(?DateTime $confirmedByUser): void
    {
        $this->confirmedByUser = $confirmedByUser;
    }

    /**
     * @throws Exception
     */
    final public function confirmByUser(): void
    {
        if (!$this->confirmedByUser) {
            $this->confirmedByUser = new DateTime();
        }
    }
}
