<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use OswisOrg\OswisCoreBundle\Utils\AgeUtils;

/**
 * Trait adds birthDate field.
 */
trait BirthDateTrait
{
    /**
     * Birth date.
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected ?DateTime $birthDate = null;

    /**
     * @throws Exception
     */
    public function getAge(?DateTime $referenceDateTime = null): ?int
    {
        return AgeUtils::getAgeFromBirthDate($this->birthDate, $referenceDateTime);
    }

    /**
     * @throws Exception
     */
    public function getAgeDecimal(?DateTime $referenceDateTime = null): ?int
    {
        return AgeUtils::getAgeDecimalFromBirthDate($this->birthDate, $referenceDateTime);
    }

    /**
     * Get birth date.
     */
    public function getBirthDate(): ?DateTime
    {
        if ($this->birthDate instanceof DateTime) {
            $this->birthDate->setTime(0, 0);
        }

        return $this->birthDate;
    }

    /**
     * Set date and time of entity update.
     */
    public function setBirthDate(?DateTime $birthDate): void
    {
        if ($birthDate instanceof DateTime) {
            $birthDate->setTime(0, 0);
        }
        $this->birthDate = $birthDate;
    }
}
