<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Catalog;

/** Položka nabídky „Vložit": proměnná (`{{ … }}`) nebo podmínka (`{% if … %}`) s českým názvem. */
final readonly class MailCatalogItem
{
    public const string VARIABLE = 'variable';
    public const string CONDITION = 'condition';

    public function __construct(
        public string $group,
        public string $label,
        public string $expression,
        public string $kind = self::VARIABLE,
    ) {
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
