<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use Exception;
use InvalidArgumentException;
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
        return $type === $this->getType();
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
     * @throws InvalidArgumentException
     */
    public function setType(?string $type): void
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

    public static function getAllowedTypes(): array
    {
        return array_merge(static::getAllowedTypesDefault(), static::getAllowedTypesCustom());
    }

    abstract public static function getAllowedTypesDefault(): array;

    abstract public static function getAllowedTypesCustom(): array;
}
