<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Exception;
use InvalidArgumentException;
use function in_array;

/**
 * Trait adds type field and some attended fiends and functions.
 */
trait TypeTrait
{
    /**
     * Type of this event.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $type = null;

    final public function getType(): ?string
    {
        try {
            self::checkType($this->type);
        } catch (Exception $e) {
            return null;
        }

        return $this->type;
    }

    /**
     * @throws InvalidArgumentException
     */
    final public function setType(?string $type): void
    {
        $type = '' === $type ? null : $type;
        self::checkType($type);
        $this->type = $type;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function checkType(?string $typeName): bool
    {
        if (!$typeName || '' === $typeName || in_array($typeName, self::getAllowedTypes(), true)) {
            return true;
        }
        throw new InvalidArgumentException('Typ "'.$typeName.'" v události není povolen.');
    }

    final public static function getAllowedTypes(): array
    {
        return array_merge(static::getAllowedTypesDefault(), static::getAllowedTypesCustom());
    }

    abstract public static function getAllowedTypesDefault(): array;

    abstract public static function getAllowedTypesCustom(): array;
}
