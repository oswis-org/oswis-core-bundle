<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait MaintenancePeriodTrait
{
    use NameableBasicTrait;
    use DateRangeTrait;
    use PlainTextReasonTrait;

    /**
     * True if accommodation is not available during this period.
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    protected ?bool $accommodationNotAvailable = null;

    public function getAccommodationNotAvailable(): bool
    {
        return $this->accommodationNotAvailable ?? false;
    }

    /**
     * @param bool $accommodationNotAvailable
     */
    public function setAccommodationNotAvailable(?bool $accommodationNotAvailable = null): void
    {
        $this->accommodationNotAvailable = $accommodationNotAvailable;
    }
}
