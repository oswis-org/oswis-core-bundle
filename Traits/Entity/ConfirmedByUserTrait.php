<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait ConfirmedByUserTrait
 * @package Zakjakub\OswisCoreBundle\Traits\Entity
 */
trait ConfirmedByUserTrait
{

    /**
     * Confirmed by user.
     * @var \DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected $confirmedByUser;


    /**
     * @return bool
     */
    final public function isConfirmedByUser(): bool
    {
        return $this->confirmedByUser ? true : false;
    }

    /**
     * @return \DateTime|null
     */
    final public function getConfirmedByUser(): ?\DateTime
    {
        return $this->confirmedByUser;
    }

    /**
     * @param \DateTime|null $confirmedByUser
     */
    final public function setConfirmedByUser(?\DateTime $confirmedByUser): void
    {
        $this->confirmedByUser = $confirmedByUser;
    }

    /**
     * @throws \Exception
     */
    final public function confirmByUser(): void
    {
        if (!$this->confirmedByUser) {
            $this->confirmedByUser = new \DateTime();
        }
    }

}
