<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use function floor;

/**
 * Trait adds createdDateTime and updatedDateTime fields.
 *
 * Trait adds fields *createdDateTime* and *updatedDateTime* and allows to access them.
 * * _**createdDateTime**_ contains date and time when entity was created
 * * _**updatedDateTime**_ contains date and time when entity was updated/changed
 */
trait TimestampableTrait
{
    use \Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

    public function getCreatedDaysAgo(?bool $decimal = false): ?int
    {
        $ago = $this->getCreatedAt()->diff(new DateTime())->days;
        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : floor($ago);
    }

    /**
     * Get date and time of entity creation.
     * @return DateTimeInterface
     */
    public function getCreatedDateTime(): ?DateTimeInterface
    {
        return $this->getCreatedAt();
    }

    public function getUpdatedDaysAgo(?bool $decimal = false): ?int
    {
        $ago = $this->getUpdatedAt()->diff(new DateTime())->days;
        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : floor($ago);
    }

    /**
     * Get date and time of entity update.
     * @return DateTimeInterface
     */
    public function getUpdatedDateTime(): ?DateTimeInterface
    {
        return $this->getUpdatedAt();
    }
}
