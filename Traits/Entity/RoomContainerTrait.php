<?php
/** @noinspection PhpDocRedundantThrowsInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

/**
 * Trait adds getters and setters for container of entity with room fields.
 */
trait RoomContainerTrait
{
    use NameableBasicContainerTrait;
    use DateTimeContainerTrait;

    /**
     * @param string|null $floor
     *
     * @throws \Exception
     */
    final public function setFloor(?string $floor): void
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
    final public function getFloor(?\DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getFloor();
    }

    /**
     * @param string|null $numberOfBeds
     *
     * @throws \Exception
     */
    final public function setNumberOfBeds(?string $numberOfBeds): void
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
     * @param string|null $numberOfExtraBeds
     *
     * @throws \Exception
     */
    final public function setNumberOfExtraBeds(?string $numberOfExtraBeds): void
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
     * @param string|null $numberOfAnimals
     *
     * @throws \Exception
     */
    final public function setNumberOfAnimals(?string $numberOfAnimals): void
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
