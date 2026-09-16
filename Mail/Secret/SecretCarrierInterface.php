<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Secret;

/**
 * Something passed into a mail template that must never survive in the stored copy —
 * a one-time token, a generated password.
 *
 * The primary protection is the `data-sensitive` marker in the template / code block
 * ({@see MailSecretRedactor}), which works even where the template data is unknown.
 * This interface is the backstop for a forgotten marker: when the sender does know the
 * data, it strips the values themselves as well.
 */
interface SecretCarrierInterface
{
    /**
     * Values that must not appear in a stored mail body.
     *
     * @return list<string>
     */
    public function getMailSecrets(): array;
}
