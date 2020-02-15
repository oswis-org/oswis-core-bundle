<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTimeInterface;

interface BirthDateInterface
{
    public function getBirthDate(): ?DateTimeInterface;

    public function setBirthDate(?DateTimeInterface $birthDate): void;

    public function getAge(?DateTimeInterface $referenceDateTime = null): ?int;

    public function getAgeDecimal(?DateTimeInterface $referenceDateTime = null): ?int;
}
