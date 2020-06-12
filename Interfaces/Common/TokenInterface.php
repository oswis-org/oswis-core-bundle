<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface TokenInterface extends BasicInterface, TypeInterface
{
    public function getToken(): string;

    public function getFirstUsedAt(): ?DateTime;

    public function getLastUsedAt(): ?DateTime;

    public function getTimesUsed(): int;

    public function canBeUsed(): bool;

    public function use(): void;

    public function isMultipleUseAllowed(): bool;

    public function setMultipleUseAllowed(bool $multipleUseAllowed): void;

    public function setValidHours(int $hours): void;

    public function getValidHours(): int;

    public function setEmail(?string $email): void;

    public function getEmail(): ?string;

    public function isValidAt(?DateTime $dateTime = null): bool;

    public function isUsed(): bool;

    public function getValidUntil(): DateTime;
}
