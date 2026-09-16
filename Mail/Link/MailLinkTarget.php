<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Link;

/**
 * Cíl odkazu v textu mailu — `{{ odkaz('klíč') }}` nebo `{{ odkaz('klíč', 'hodnota') }}`.
 *
 * PROČ tudy a ne rovnou adresou: adresa se dopočítá **až při odeslání**, takže odkaz nezastará, když
 * se stránka přestěhuje, a editor si zápis umí přečíst zpátky a nabídnout v dialogu týž cíl (kdyby
 * v adrese byl libovolný Twig, šel by změnit jen v režimu kódu).
 */
final readonly class MailLinkTarget
{
    /**
     * @param string                          $key         klíč do `odkaz('…')`; ustálený, ne odvozený z názvu
     * @param string                          $label       český název v nabídce
     * @param string                          $group       skupina v nabídce („Přihlášky", „Portál", …)
     * @param string                          $route       jméno routy, ze které se skládá adresa
     * @param string|null                     $parameter   jméno parametru routy, který vybírá uživatel (null = cíl bez výběru)
     * @param list<array{value: string, label: string}> $options nabídka hodnot parametru (prázdné = volné pole)
     * @param array<string, string|int>       $fixedParams parametry routy, které uživatel nevybírá
     * @param string|null                     $hint        věta pod výběrem (co odkaz komu ukáže)
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $group,
        public string $route,
        public ?string $parameter = null,
        public array $options = [],
        public array $fixedParams = [],
        public ?string $hint = null,
    ) {
    }

    /**
     * Tvar pro editor (`MailEditorConfig::toArray()`); čte ho TypeScript `assets/mail-editor/link-target.ts`.
     *
     * @return array{key: string, label: string, group: string, parameter: string|null,
     *     options: list<array{value: string, label: string}>, hint: string|null}
     */
    public function toArray(): array
    {
        return [
            'key'       => $this->key,
            'label'     => $this->label,
            'group'     => $this->group,
            'parameter' => $this->parameter,
            'options'   => $this->options,
            'hint'      => $this->hint,
        ];
    }
}
