<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use function date_create;
use function floor;

/**
 * Trait adds dateTime field.
 */
trait DateTimeTrait
{
    /**
     * Date and time.
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected ?DateTime $dateTime = null;

    public function getDaysAgo(?bool $decimal = false): ?int
    {
        try {
            if ($this->getDateTime()) {
                $ago = $this->getDateTime()->diff(date_create())->days;

                return $decimal ? $ago : floor($ago);
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get date and time.
     *
     * @return DateTime
     */
    public function getDateTime(): ?DateTime
    {
        return $this->dateTime;
    }

    /**
     * Set date and time.
     *
     * @param DateTime $dateTime
     */
    public function setDateTime(?DateTime $dateTime = null): void
    {
        $this->dateTime = $dateTime ? clone $dateTime : null;
    }
}
