<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait MaintenancePeriodTrait
{
    use NameableBasicTrait;
    use DateRangeTrait;
    use PlainTextReasonTrait;

    /**
     * True if accommodation is not available during this period.
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $accommodationNotAvailable;

    /**
     * @return bool
     */
    final public function getAccommodationNotAvailable(): bool
    {
        return $this->accommodationNotAvailable ?? false;
    }

    /**
     * @param bool $accommodationNotAvailable
     */
    final public function setAccommodationNotAvailable(?bool $accommodationNotAvailable = null): void
    {
        $this->accommodationNotAvailable = $accommodationNotAvailable;
    }
}
