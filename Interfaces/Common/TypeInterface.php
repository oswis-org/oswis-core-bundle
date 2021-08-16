<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

interface TypeInterface
{
    public static function checkType(?string $typeName): bool;

    public static function getAllowedTypes(): array;

    public static function getAllowedTypesDefault(): array;

    public static function getAllowedTypesCustom(): array;

    public function isType(?string $type): bool;

    public function getType(): ?string;

    public function setType(?string $type): void;
}
