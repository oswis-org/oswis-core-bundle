<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds createdDateTime and updatedDateTime fields
 *
 * Trait adds fields *createdDateTime* and *updatedDateTime* and allows to access them.
 * * _**createdDateTime**_ contains date and time when entity was created
 * * _**updatedDateTime**_ contains date and time when entity was updated/changed
 *
 */
trait TimestampableTrait
{

    /**
     * Date and time of entity creation.
     * @var \DateTime
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     */
    protected $createdDateTime;

    /**
     * Date and time of entity update.
     * @var \DateTime
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     */
    protected $updatedDateTime;


    final public function getCreatedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->getCreatedDateTime()) {
            return null;
        }
        $ago = $this->getCreatedDateTime()->diff(\date_create())->days;

        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : \floor($ago);
    }

    /**
     * Get date and time of entity creation
     *
     * @return \DateTime
     */
    final public function getCreatedDateTime(): ?\DateTime
    {
        return $this->createdDateTime;
    }

    final public function getUpdatedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->getUpdatedDateTime()) {
            return null;
        }
        $ago = $this->getUpdatedDateTime()->diff(\date_create())->days;

        if (!$ago) {
            return null;
        }

        return $decimal ? $ago : \floor($ago);
    }

    /**
     * Get date and time of entity update
     *
     * @return \DateTime
     */
    final public function getUpdatedDateTime(): ?\DateTime
    {
        return $this->updatedDateTime;
    }
}
