<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\AgeUtils;

/**
 * Trait adds createdDateTime and updatedDateTime fields.
 *
 * Trait adds fields *createdDateTime* and *updatedDateTime* and allows to access them.
 * * _**createdDateTime**_ contains date and time when entity was created
 * * _**updatedDateTime**_ contains date and time when entity was updated/changed
 */
trait AgeRangeTrait
{
    /**
     * Minimal age of person in this group.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="smallint", nullable=true)
     */
    protected ?int $minAge = null;

    /**
     * Maximal age of person in this group.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="smallint", nullable=true)
     */
    protected ?int $maxAge = null;

    /**
     * True if person belongs to this age range (at some moment - referenceDateTime).
     *
     * @param DateTime      $birthDate         BirthDate for age calculation
     * @param DateTime|null $referenceDateTime Reference date, default is _now_
     *
     * @return bool True if belongs to age range
     *
     * @throws Exception
     */
    final public function containsBirthDate(DateTime $birthDate, DateTime $referenceDateTime = null): bool
    {
        return AgeUtils::isBirthDateInRange($birthDate, $this->minAge, $this->maxAge, $referenceDateTime);
    }

    final public function agesDiff(): int
    {
        return ($this->getMaxAge() && $this->getMinAge()) ? $this->getMaxAge() - $this->getMinAge() : null;
    }

    final public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    final public function setMaxAge(?int $maxAge): void
    {
        $this->maxAge = $maxAge;
    }

    final public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    /**
     * @param int $minAge
     */
    final public function setMinAge(?int $minAge): void
    {
        $this->minAge = $minAge;
    }


}
