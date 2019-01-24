<?php
/** @noinspection PhpDocRedundantThrowsInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds getters and setters for container of entity with room fields.
 */
trait RoomContainerTrait
{
    use NameableBasicContainerTrait;
    use DateTimeContainerTrait;

    /**
     * @param int|null $floor
     *
     * @throws \Exception
     */
    final public function setFloor(?int $floor): void
    {
        if ($this->getFloor() !== $floor) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setFloor($floor);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int
     * @throws \Exception
     */
    final public function getFloor(?\DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getFloor();
    }

    /**
     * @param int|null $numberOfBeds
     *
     * @throws \Exception
     */
    final public function setNumberOfBeds(?int $numberOfBeds): void
    {
        if ($this->getNumberOfBeds() !== $numberOfBeds) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setNumberOfBeds($numberOfBeds);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int
     * @throws \Exception
     */
    final public function getNumberOfBeds(?\DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getNumberOfBeds();
    }

    /**
     * @param int|null $numberOfExtraBeds
     *
     * @throws \Exception
     */
    final public function setNumberOfExtraBeds(?int $numberOfExtraBeds): void
    {
        if ($this->getNumberOfExtraBeds() !== $numberOfExtraBeds) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setNumberOfExtraBeds($numberOfExtraBeds);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int
     * @throws \Exception
     */
    final public function getNumberOfExtraBeds(?\DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getNumberOfExtraBeds();
    }

    /**
     * @param int|null $numberOfAnimals
     *
     * @throws \Exception
     */
    final public function setNumberOfAnimals(?int $numberOfAnimals): void
    {
        if ($this->getNumberOfAnimals() !== $numberOfAnimals) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setNumberOfAnimals($numberOfAnimals);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int
     * @throws \Exception
     */
    final public function getNumberOfAnimals(?\DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getNumberOfAnimals();
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return string
     * @throws \Exception
     */
    final public function getCapacity(?\DateTime $dateTime = null): string
    {
        $revision = $this->getRevisionByDate($dateTime);
        $output = $revision->getNumberOfBeds().'/';
        $output .= $revision->getNumberOfExtraBeds().'/';
        $output .= $revision->getNumberOfAnimals();

        return $output;
    }
}
