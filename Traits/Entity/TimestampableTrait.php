<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
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

    public function getCreatedDateTime(): ?DateTime
    {
        $createdAt = $this->getCreatedAt();
        assert($createdAt instanceof DateTime);

        return $createdAt;
    }

    public function getUpdatedDaysAgo(?bool $decimal = false): ?int
    {
        $ago = $this->getUpdatedAt()->diff(new DateTime())->days;
        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : floor($ago);
    }

    public function getUpdatedDateTime(): ?DateTime
    {
        $updatedAt = $this->getUpdatedAt();
        assert($updatedAt instanceof DateTime);

        return $updatedAt;
    }
}
