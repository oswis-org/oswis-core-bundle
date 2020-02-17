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
    /**
     * Date and time of entity creation.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     */
    protected ?DateTime $createdDateTime = null;

    /**
     * Date and time of entity update.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     */
    protected ?DateTime $updatedDateTime = null;

    public function getCreatedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->getCreatedDateTime()) {
            return null;
        }
        $ago = $this->getCreatedDateTime()->diff(new DateTime())->days;
        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : floor($ago);
    }

    public function getCreatedDateTime(): ?DateTime
    {
        return $this->createdDateTime;
    }

    public function getUpdatedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->getUpdatedDateTime()) {
            return null;
        }
        $ago = $this->getUpdatedDateTime()->diff(new DateTime())->days;
        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : floor($ago);
    }

    public function getUpdatedDateTime(): ?DateTime
    {
        return $this->updatedDateTime;
    }
}
