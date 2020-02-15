<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\AgeUtils;

/**
 * Trait adds birthDate field.
 */
trait BirthDateTrait
{
    /**
     * Birth date.
     * @var DateTimeInterface|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected ?DateTimeInterface $birthDate = null;

    /**
     * @throws Exception
     */
    public function getAge(?DateTimeInterface $referenceDateTime = null): ?int
    {
        return AgeUtils::getAgeFromBirthDate($this->birthDate, $referenceDateTime);
    }

    /**
     * @throws Exception
     */
    public function getAgeDecimal(?DateTimeInterface $referenceDateTime = null): ?int
    {
        return AgeUtils::getAgeDecimalFromBirthDate($this->birthDate, $referenceDateTime);
    }

    /**
     * Get birth date.
     */
    public function getBirthDate(): ?DateTimeInterface
    {
        if ($this->birthDate instanceof DateTime) {
            $this->birthDate->setTime(0, 0);
        }

        return $this->birthDate;
    }

    /**
     * Set date and time of entity update.
     */
    public function setBirthDate(?DateTimeInterface $birthDate): void
    {
        if ($birthDate instanceof DateTime) {
            $birthDate->setTime(0, 0);
        }
        $this->birthDate = $birthDate;
    }
}
