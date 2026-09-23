<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Parent;

/**
 * Obálka, ze které může šablona e-mailu vycházet (pole „Vychází z" — rodič šablony,
 * {@see \OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate::slozitZdroj()}).
 */
final readonly class MailParent
{
    public function __construct(
        /** Twig jméno šablony-rodiče (`@Bundle/…html.twig`). */
        public string $template,
        /** Lidský popisek do nabídky — co obálka mailu dodá. Prázdný = poskytovatel jen doplňuje `bloky`. */
        public string $label,
        /**
         * Bloky obálky, které má smysl v šabloně přepsat: název → lidský popisek (editor jím nadepíše
         * úsek místo holého `{% block content_inner %}`). Víc poskytovatelů téže obálky se sčítá —
         * aplikace tak popíše bloky, které přidává svým přebitím obálky.
         *
         * @var array<string, string>
         */
        public array $bloky = [],
    ) {
    }
}
