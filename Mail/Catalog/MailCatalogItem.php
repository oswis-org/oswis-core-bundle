<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Catalog;

/** Položka nabídky „Vložit": proměnná (`{{ … }}`) nebo podmínka (`{% if … %}`) s českým názvem. */
final readonly class MailCatalogItem
{
    public const string VARIABLE = 'variable';
    public const string CONDITION = 'condition';

    /**
     * @param bool        $mayBeEmpty výraz smí být u části příjemců prázdný (koncovka `a` u mužů) — kontrola
     *                                na to neupozorňuje
     * @param string|null $chip       krátký text štítku v editoru („-a", „Oslovení"); bez něj = `label`
     */
    public function __construct(
        public string $group,
        public string $label,
        public string $expression,
        public string $kind = self::VARIABLE,
        public bool $mayBeEmpty = false,
        public ?string $chip = null,
    ) {
    }

    /** Výraz bez rozdílů v mezerách — pro porovnání s tím, co autor napsal. */
    public static function normalize(string $expression): string
    {
        return (string) preg_replace('/\s+/', '', $expression);
    }

    public function token(): string
    {
        return self::CONDITION === $this->kind
            ? "{% if {$this->expression} %}\n  …\n{% endif %}"
            : '{{ '.$this->expression.' }}';
    }

    /** Kořenová proměnná výrazu (`participant.event(false).name` → `participant`). */
    public function rootName(): string
    {
        return 1 === preg_match('/^[A-Za-z_][A-Za-z0-9_]*/', $this->expression, $match) ? $match[0] : '';
    }
}
