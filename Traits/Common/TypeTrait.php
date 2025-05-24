<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use Exception;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use function in_array;

/**
 * Trait adds type field and some attended fiends and functions.
 */
trait TypeTrait
{
    /** Type as text. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $type = null;

    public function isType(?string $type): bool
    {
        return $this->getType() === $type;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        try {
            /** @phpstan-ignore-next-line */
            self::checkType($this->type);
        } catch (Exception) {
            return null;
        }

        return $this->type;
    }

    /**
     * @param  string|null  $type
     *
     * @throws InvalidTypeException
     */
    public function setType(?string $type): void
    {
        $type = empty($type) ? null : $type;
        self::checkType($type);
        $this->type = $type;
    }

    /**
     * @param  string|null  $typeName
     *
     * @return bool
     * @throws InvalidTypeException
     */
    public static function checkType(?string $typeName): bool
    {
        if (empty($typeName) || in_array($typeName, self::getAllowedTypes(), true)) {
            return true;
        }
        throw new InvalidTypeException($typeName);
    }

    public static function getAllowedTypes(): array
    {
        return array_merge(static::getAllowedTypesDefault(), static::getAllowedTypesCustom());
    }

    public static function getAllowedTypesDefault(): array
    {
        return [''];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }
}
