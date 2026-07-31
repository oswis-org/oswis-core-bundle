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
     * Kontaktní typy — v PDF se z nich stane klikací odkaz (`mailto:` / `tel:`), v CSV a XLSX
     * zůstává holá hodnota. Jsou to samostatné typy, ne jen formátování: PDF se čte na tabletu
     * u stolu a tým z něj potřebuje volat a psát rovnou, ne přepisovat čísla ručně.
     */
    public const string TYPE_EMAIL    = 'email';
    public const string TYPE_PHONE    = 'phone';

    public const string ALIGN_LEFT  = 'left';
    public const string ALIGN_RIGHT = 'right';

    /**
     * @param \Closure(object): mixed $valueExtractor
     * @param string|null $align Zarovnání v PDF; null = odvodit z typu (čísla vpravo, zbytek vlevo).
     *                           Explicitně se hodí tam, kde je hodnota číselná, ale NESMÍ se sčítat
     *                           (procenta, ID) — sčítají se totiž všechny sloupce typu NUMBER.
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly \Closure $valueExtractor,
        public readonly bool $defaultSelected = true,
        public readonly string $type = self::TYPE_TEXT,
        public readonly ?string $align = null,
    ) {
    }

    public function extract(object $entity): mixed
    {
        return ($this->valueExtractor)($entity);
    }

    public function getAlign(): string
    {
        return $this->align ?? (self::TYPE_NUMBER === $this->type ? self::ALIGN_RIGHT : self::ALIGN_LEFT);
    }

    /**
     * Předpona odkazu pro PDF, nebo null když se sloupec nelinkuje.
     */
    public function getLinkScheme(): ?string
    {
        return match ($this->type) {
            self::TYPE_EMAIL => 'mailto:',
            self::TYPE_PHONE => 'tel:',
            default          => null,
        };
    }
}
