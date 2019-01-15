<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds dateTime field
 *
 */
trait EntityDateTimeTrait
{

    /**
     * Date and time.
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected $dateTime;

    final public function getDaysAgo(?bool $decimal = false): ?int
    {
        try {
            if ($this->getDateTime()) {
                $ago = $this->getDateTime()->diff(\date_create());

                return $decimal ? $ago : \floor($ago);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get date and time.
     *
     * @return \DateTime
     */
    final public function getDateTime(): ?\DateTime
    {
        return $this->dateTime ?? null;
    }

    /**
     * Set date and time.
     *
     * @param \DateTime $dateTime
     */
    final public function setDateTime(?\DateTime $dateTime = null): void
    {
        $this->dateTime = $dateTime ? clone $dateTime : null;
    }
}