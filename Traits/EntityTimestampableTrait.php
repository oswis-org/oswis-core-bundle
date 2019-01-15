<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait adds createdDateTime and updatedDateTime fields
 *
 * Trait adds fields *createdDateTime* and *updatedDateTime* and allows to access them.
 * * _**createdDateTime**_ contains date and time when entity was created
 * * _**updatedDateTime**_ contains date and time when entity was updated/changed
 *
 */
trait EntityTimestampableTrait
{

    /**
     * Date and time of entity creation
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdDateTime;

    /**
     * Date and time of entity update
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default" : null})
     *
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedDateTime;

    final public function getCreatedDaysAgo(?bool $decimal = false): int
    {
        $ago = $this->getCreatedDateTime()->diff(\date_create());

        return $decimal ? $ago : \floor($ago);
    }

    /**
     * Get date and time of entity creation
     *
     * @return \DateTime
     */
    final public function getCreatedDateTime(): \DateTime
    {
        return $this->createdDateTime;
    }

    final public function getUpdatedDaysAgo(?bool $decimal = false): int
    {
        $ago = $this->getUpdatedDateTime()->diff(\date_create());

        return $decimal ? $ago : \floor($ago);
    }

    /**
     * Get date and time of entity update
     *
     * @return \DateTime
     */
    final public function getUpdatedDateTime(): \DateTime
    {
        return $this->updatedDateTime;
    }
}
