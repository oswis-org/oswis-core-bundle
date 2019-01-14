<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Zakjakub\OswisResourcesBundle\Utils\AgeUtils;


/**
 * Trait adds birthDate field
 *
 */
trait EntityBirthDateTrait
{

    /**
     * Birth date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected $birthDate;

    /**
     * @param \DateTime|null $referenceDateTime
     *
     * @return int|null
     * @throws \Exception
     */
    final public function getAge(\DateTime $referenceDateTime = null): ?int
    {
        return AgeUtils::getAgeFromBirthDate($this->birthDate, $referenceDateTime);
    }

    /**
     * @param \DateTime|null $referenceDateTime
     *
     * @return int|null
     * @throws \Exception
     */
    final public function getAgeDecimal(\DateTime $referenceDateTime = null): ?int
    {
        return AgeUtils::getAgeDecimalFromBirthDate($this->birthDate, $referenceDateTime);
    }

    /**
     * Get birth date.
     *
     * @return \DateTime|null
     */
    final public function getBirthDate(): ?\DateTime
    {
        $this->birthDate->setTime(0, 0);

        return $this->birthDate;
    }

    /**
     * Set date and time of entity update
     *
     * @param \DateTime $birthDate
     *
     * @throws \Exception
     */
    final public function setBirthDate(?\DateTime $birthDate): void
    {
        if ($birthDate) {
            $birthDate = new \DateTime($birthDate->getTimestamp());
            $birthDate->setTime(0, 0);
        }
        $this->birthDate = $birthDate;
    }
}
