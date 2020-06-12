<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use Exception;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use function in_array;

/**
 * Trait adds type field and some attended fiends and functions.
 */
trait TypeTrait
{
    /**
     * Type as text.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $type = null;

    public function isType(?string $type): bool
    {
        return $this->getType() === $type;
    }

    public function getType(): ?string
    {
        try {
            self::checkType($this->type);
        } catch (Exception $e) {
            return null;
        }

        return $this->type;
    }

    /**
     * @param string|null $type
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
     * @param string|null $typeName
     *
     * @return bool
     * @throws InvalidTypeException
     */
    public static function checkType(?string $typeName): bool
    {
        if (!$typeName || '' === $typeName || in_array($typeName, self::getAllowedTypes(), true)) {
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
