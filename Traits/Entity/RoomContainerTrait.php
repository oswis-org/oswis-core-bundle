<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds getters and setters for container of entity with room fields.
 */
trait RoomContainerTrait
{
    use NameableBasicContainerTrait;
    use DateTimeContainerTrait;

    public function setFloor(?int $floor): void
    {
        if ($this->getFloor() !== $floor) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setFloor($floor);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @return int
     */
    public function getFloor(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getFloor();
    }

    public function setNumberOfBeds(?int $numberOfBeds): void
    {
        if ($this->getNumberOfBeds() !== $numberOfBeds) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setNumberOfBeds($numberOfBeds);
            $this->addRevision($newRevision);
        }
    }

    public function getNumberOfBeds(?DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getNumberOfBeds();
    }

    public function setNumberOfExtraBeds(?int $numberOfExtraBeds): void
    {
        if ($this->getNumberOfExtraBeds() !== $numberOfExtraBeds) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setNumberOfExtraBeds($numberOfExtraBeds);
            $this->addRevision($newRevision);
        }
    }

    public function getNumberOfExtraBeds(?DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getNumberOfExtraBeds();
    }

    public function setNumberOfAnimals(?int $numberOfAnimals): void
    {
        if ($this->getNumberOfAnimals() !== $numberOfAnimals) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setNumberOfAnimals($numberOfAnimals);
            $this->addRevision($newRevision);
        }
    }

    public function getNumberOfAnimals(?DateTime $dateTime = null): int
    {
        return $this->getRevisionByDate($dateTime)->getNumberOfAnimals();
    }

    public function getCapacity(?DateTime $dateTime = null): string
    {
        $revision = $this->getRevisionByDate($dateTime);
        $output = $revision->getNumberOfBeds().'/';
        $output .= $revision->getNumberOfExtraBeds().'/';
        $output .= $revision->getNumberOfAnimals();

        return $output;
    }
}
