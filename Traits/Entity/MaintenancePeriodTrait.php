<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait MaintenancePeriodTrait
{
    use NameableBasicTrait;
    use DateRangeTrait;
    use PlainTextReasonTrait;

    /**
     * True if accommodation is not available during this period.
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    protected ?bool $accommodationNotAvailable;

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
