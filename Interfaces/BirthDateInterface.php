<?php

namespace OswisOrg\OswisCoreBundle\Interfaces;

use DateTime;

interface BirthDateInterface
{
    public function getBirthDate(): ?DateTime;

    public function setBirthDate(?DateTime $birthDate): void;

    public function getAge(?DateTime $referenceDateTime = null): ?int;

    public function getAgeDecimal(?DateTime $referenceDateTime = null): ?int;
}
