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
     * Hotový cíl odkazu pro PDF, nebo null když se sloupec nelinkuje / hodnota je prázdná.
     *
     * ⚠️ **Proč u telefonu tečky:** mPDF rozhoduje, jestli je odkaz externí, podle toho, jestli
     * obsahuje TEČKU (`Mpdf.php`: „assuming every external link has a dot indicating extension").
     * `tel:730564041` tečku nemá → mPDF ho považuje za vnitřní kotvu a v PDF nevznikne žádný
     * odkaz (ověřeno: bez tečky 0 anotací, s tečkou se vytvoří). Tečka je přitom podle RFC 3966
     * legitimní vizuální oddělovač v `tel:` URI a telefon si ji při vytáčení odstraní — není to
     * tedy obcházení, ale platný zápis, který navíc projde přes mPDF.
     */
    public function buildHref(mixed $value): ?string
    {
        if (!is_scalar($value) && !$value instanceof \Stringable) {
            return null;
        }
        $text = trim((string) $value);
        if ('' === $text) {
            return null;
        }

        return match ($this->type) {
            self::TYPE_EMAIL => 'mailto:'.$text,
            self::TYPE_PHONE => self::telHref($text),
            default          => null,
        };
    }

    /**
     * `tel:` URI s tečkami jako oddělovači (RFC 3966): „+420 777 123 456" → „tel:+420.777.123.456".
     */
    private static function telHref(string $phone): ?string
    {
        $plus = str_starts_with(ltrim($phone), '+');
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ('' === $digits) {
            return null;
        }
        // Skupiny po třech zprava — česká konvence zápisu čísla.
        $grouped = trim(chunk_split(strrev($digits), 3, '.'), '.');
        $grouped = implode('.', array_reverse(explode('.', $grouped)));
        $grouped = implode('.', array_map('strrev', explode('.', $grouped)));

        return 'tel:'.($plus ? '+' : '').$grouped;
    }
}
