<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

/**
 * Jeden sloupec exportu: stabilní klíč, český popisek, jak získat hodnotu z entity.
 */
final class ExportColumn
{
    public const string TYPE_TEXT     = 'text';
    public const string TYPE_DATE     = 'date';
    public const string TYPE_DATETIME = 'datetime';
    public const string TYPE_NUMBER   = 'number';
    public const string TYPE_BOOL     = 'bool';

    /**
     * @param \Closure(object): mixed $valueExtractor
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly \Closure $valueExtractor,
        public readonly bool $defaultSelected = true,
        public readonly string $type = self::TYPE_TEXT,
    ) {
    }

    public function extract(object $entity): mixed
    {
        return ($this->valueExtractor)($entity);
    }
}
