<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds description field
 */
trait EntityZtpOrPetTrait
{

    /**
     * Person is ZTP(P).
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $ztp;

    /**
     * Person is ZTP(P) accompaniment.
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $ztpAccompaniment;

    /**
     * Pet, not person.
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $pet;

    /**
     * @return bool
     */
    final public function isZtp(): bool
    {
        return $this->ztp ?? false;
    }

    /**
     * @param bool $ztp
     */
    final public function setZtp(bool $ztp): void
    {
        $this->ztp = $ztp;
    }

    /**
     * @return bool
     */
    final public function isZtpAccompaniment(): bool
    {
        return $this->ztpAccompaniment ?? false;
    }

    /**
     * @param bool $ztpAccompaniment
     */
    final public function setZtpAccompaniment(bool $ztpAccompaniment): void
    {
        $this->ztpAccompaniment = $ztpAccompaniment;
    }

    /**
     * @return bool
     */
    final public function isPet(): bool
    {
        return $this->pet ?? false;
    }

    /**
     * @param bool $pet
     */
    final public function setPet(bool $pet): void
    {
        $this->pet = $pet;
    }
}
