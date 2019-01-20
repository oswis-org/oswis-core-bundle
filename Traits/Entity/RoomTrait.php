<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds room fields.
 */
trait RoomTrait
{
    use NameableBasicTrait;
    use DateTimeTrait;

    /**
     * Floor number.
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $floor;

    /**
     * Number of regular beds.
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $numberOfBeds;

    /**
     * Number of extra beds.
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $numberOfExtraBeds;

    /**
     * Number of animals.
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $numberOfAnimals;

    /**
     * @return int|null
     */
    final public function getFloor(): ?int
    {
        return $this->floor;
    }

    /**
     * @param int|null $floor
     */
    final public function setFloor(?int $floor): void
    {
        $this->floor = $floor;
    }

    /**
     * @return int
     */
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

    /**
     * @return int
     */
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

    /**
     * @return int
     */
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
