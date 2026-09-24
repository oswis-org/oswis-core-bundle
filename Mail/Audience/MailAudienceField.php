<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Audience;

/**
 * Údaj pro stavebnici podmínky v editoru mailu („Zbývá zaplatit je více než 0") — dávka 2c, bod 7.
 * Klíč je funkce slovníku, nad kterým modul podmínku vyhodnocuje (v kalendáři filtr přihlášek).
 */
final readonly class MailAudienceField
{
    public const string BOOL = 'bool';
    public const string NUMBER = 'number';
    public const string CHOICE = 'choice';
    public const string FLAG = 'flag';

    /**
     * @param list<array{value: string, label: string, group?: string}> $options volby u výběru a příznaku
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $type,
        public array $options = [],
    ) {
    }

    /** @return array{key: string, label: string, type: string, options: list<array{value: string, label: string, group?: string}>} */
    public function toArray(): array
    {
        return ['key' => $this->key, 'label' => $this->label, 'type' => $this->type, 'options' => $this->options];
    }
}
