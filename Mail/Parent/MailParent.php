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
        /** Lidský popisek do nabídky — co obálka mailu dodá. */
        public string $label,
    ) {
    }
}
