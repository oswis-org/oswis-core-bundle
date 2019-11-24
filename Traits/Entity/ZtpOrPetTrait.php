<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds description field
 */
trait ZtpOrPetTrait
{

    /**
     * Person is ZTP(P).
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $ztp;

    /**
     * Person is ZTP(P) accompaniment.
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $ztpAccompaniment;

    /**
     * Pet, not person.
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $pet;

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
