<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds description field.
 */
trait ZtpOrPetTrait
{
    /**
     * Person is ZTP(P).
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $ztp;

    /**
     * Person is ZTP(P) accompaniment.
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $ztpAccompaniment;

    /**
     * Pet, not person.
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $pet;

    final public function isZtp(): bool
    {
        return $this->ztp ?? false;
    }

    final public function setZtp(bool $ztp): void
    {
        $this->ztp = $ztp;
    }

    final public function isZtpAccompaniment(): bool
    {
        return $this->ztpAccompaniment ?? false;
    }

    final public function setZtpAccompaniment(bool $ztpAccompaniment): void
    {
        $this->ztpAccompaniment = $ztpAccompaniment;
    }

    final public function isPet(): bool
    {
        return $this->pet ?? false;
    }

    final public function setPet(bool $pet): void
    {
        $this->pet = $pet;
    }
}
