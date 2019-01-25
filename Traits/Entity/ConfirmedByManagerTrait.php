<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait ConfirmedByManagerTrait
 * @package Zakjakub\OswisCoreBundle\Traits\Entity
 */
trait ConfirmedByManagerTrait
{


    /**
     * Confirmed by manager.
     * @var \DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    protected $confirmedByManager;


    /**
     * @return bool
     */
    final public function isConfirmedByManager(): bool
    {
        return $this->confirmedByManager ? true : false;
    }

    /**
     * @return \DateTime|null
     */
    final public function getConfirmedByManager(): ?\DateTime
    {
        return $this->confirmedByManager;
    }

    /**
     * @param \DateTime|null $confirmedByUser
     */
    final public function setConfirmedByManager(?\DateTime $confirmedByUser): void
    {
        $this->confirmedByManager = $confirmedByUser;
    }

    /**
     * @throws \Exception
     */
    final public function confirmByManager(): void
    {
        if (!$this->confirmedByManager) {
            $this->confirmedByManager = new \DateTime();
        }
    }


}
