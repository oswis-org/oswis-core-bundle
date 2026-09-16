<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Secret;

use Dom\HTMLDocument;
use Throwable;

/**
 * Removes secret parts from the copy of a mail that OSWIS stores.
 *
 * The sent message keeps everything; only the stored copy (shown in the communication
 * history to the team) loses the parts marked in the template — a generated password,
 * a one-time link. The body itself is always stored whole, nothing is thrown away.
 *
 * How a place is marked:
 *   - plain HTML inside `mj-text`: `<span data-sensitive="password">…</span>`,
 *     `<a href="…" data-sensitive="link">…</a>`;
 *   - a whole MJML component (typically `mj-button`, whose own attributes MJML drops):
 *     `css-class="oswis-secret"` — the class lands on the component's outer element and
 *     every link inside it is stripped.
 * Verified 15. 9. 2026 against the real MJML output: both markers survive compilation
 * and the markup around them (including the Outlook conditional comments) is preserved.
 *
 * Marking is the primary mechanism because it needs no template data — so even a message
 * caught by the "nothing may leave unrecorded" guard can be stored complete. Values known
 * from the template data ({@see SecretCarrierInterface}) are stripped on top of that, as a
 * backstop for a marker somebody forgot to add.
 */
final class MailSecretRedactor
{
    public const string ATTRIBUTE = 'data-sensitive';

    public const string CSS_CLASS = 'oswis-secret';

    /** Shown instead of a password, a code, anything that is not a link. */
    public const string PLACEHOLDER_VALUE = '••••••';

    /** Shown instead of a link that would work for whoever clicks it. */
    public const string PLACEHOLDER_LINK = 'odkaz skrytý';

    /** Values shorter than this are not replaced — too likely to hit ordinary text. */
    private const int MIN_SECRET_LENGTH = 6;

    /**
     * @param list<string> $secretValues values from the template data (backstop, may be empty)
     */
    public function redactHtml(string $html, array $secretValues = []): string
    {
        if ('' === trim($html)) {
            return $html;
        }
        $redacted = $this->redactMarkedElements($html);

        return $this->redactValues($redacted, $secretValues);
    }

    /**
     * Plain-text alternative is derived from the ALREADY redacted HTML, so it needs no
     * markup handling — only the value backstop.
     *
     * @param list<string> $secretValues
     */
    public function redactValues(string $text, array $secretValues): string
    {
        foreach ($secretValues as $secret) {
            if (mb_strlen($secret) < self::MIN_SECRET_LENGTH) {
                continue;
            }
            $text = str_replace($secret, self::PLACEHOLDER_VALUE, $text);
        }

        return $text;
    }

    /**
     * Collects secrets from template data — top level plus one level inside arrays, which is
     * how mail contexts are built (`['appUser' => …, 'participantToken' => …]`).
     *
     * @param array<string, mixed> $data
     *
     * @return list<string>
     */
    public function collectSecrets(array $data): array
    {
        $secrets = [];
        foreach ($data as $value) {
            if ($value instanceof SecretCarrierInterface) {
                $secrets = [...$secrets, ...$value->getMailSecrets()];
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $nested) {
                    if ($nested instanceof SecretCarrierInterface) {
                        $secrets = [...$secrets, ...$nested->getMailSecrets()];
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($secrets, static fn (string $s): bool => '' !== trim($s))));
    }

    /**
     * Marked element → its links lose the address and the text, everything else keeps its markup.
     * An element with no link inside loses its text only (the password case).
     *
     * A parse failure must never block sending: the body is then stored only with the value
     * backstop applied, and the message itself is untouched either way.
     */
    private function redactMarkedElements(string $html): string
    {
        try {
            // Jen LIBXML_NOERROR — jiné příznaky `createFromString()` odmítne výjimkou.
            $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
        } catch (Throwable) {
            return $html;
        }
        $marked = $document->querySelectorAll('['.self::ATTRIBUTE.'], .'.self::CSS_CLASS);
        if (0 === $marked->count()) {
            return $html;
        }
        foreach ($marked as $element) {
            $links = iterator_to_array($element->querySelectorAll('a[href]'));
            if ($element->hasAttribute('href')) {
                $links[] = $element;
            }
            foreach ($links as $link) {
                $link->setAttribute('href', '#');
                $link->textContent = self::PLACEHOLDER_LINK;
            }
            if ([] === $links) {
                $element->textContent = 'link' === $element->getAttribute(self::ATTRIBUTE)
                    ? self::PLACEHOLDER_LINK
                    : self::PLACEHOLDER_VALUE;
            }
        }
        // Úryvek zůstane úryvkem: parser kolem něj doplní `html`/`body`, ale do uložené kopie
        // patří jen to, co přišlo (celý e-mail z MJML svoje `<html>` má).
        $body = $document->body;
        if (null !== $body && !str_contains(strtolower($html), '<html')) {
            return $body->innerHTML;
        }

        return $document->saveHtml();
    }
}
