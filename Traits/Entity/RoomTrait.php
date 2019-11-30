<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds room fields.
 */
trait RoomTrait
{
    use NameableBasicTrait;
    use DateTimeTrait;

    /**
     * Floor number.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="smallint", nullable=true)
     */
    protected ?int $floor = null;

    /**
     * Number of regular beds.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="smallint", nullable=true)
     */
    protected ?int $numberOfBeds = null;

    /**
     * Number of extra beds.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="smallint", nullable=true)
     */
    protected ?int $numberOfExtraBeds = null;

    /**
     * Number of animals.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="smallint", nullable=true)
     */
    protected ?int $numberOfAnimals = null;

    final public function getFloor(): ?int
    {
        return $this->floor;
    }

    final public function setFloor(?int $floor): void
    {
        $this->floor = $floor;
    }

    final public function getNumberOfBeds(): int
    {
        return $this->numberOfBeds ?? 0;
    }

    /**
     * @param int $numberOfBeds
     */
    final public function setNumberOfBeds(?int $numberOfBeds): void
    {
        $this->numberOfBeds = $numberOfBeds;
    }

    final public function getNumberOfExtraBeds(): int
    {
        return $this->numberOfExtraBeds ?? 0;
    }

    /**
     * @param int| $numberOfExtraBeds
     */
    final public function setNumberOfExtraBeds(?int $numberOfExtraBeds): void
    {
        $this->numberOfExtraBeds = $numberOfExtraBeds;
    }

    final public function getNumberOfAnimals(): int
    {
        return $this->numberOfAnimals ?? 0;
    }

    /**
     * @param int| $numberOfAnimals
     */
    final public function setNumberOfAnimals(?int $numberOfAnimals): void
    {
        $this->numberOfAnimals = $numberOfAnimals;
    }
}
