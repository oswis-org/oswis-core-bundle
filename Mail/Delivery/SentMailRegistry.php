<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Delivery;

/**
 * Which messages went out through {@see \OswisOrg\OswisCoreBundle\Service\MailService} in this run.
 *
 * The guard ({@see \OswisOrg\OswisCoreBundle\EventSubscriber\UnrecordedMailGuard}) asks it whether a
 * message that just left had a delivery record. A header would do the same job, but it would travel
 * on the wire to the recipient; this stays inside the process, which is enough, because OSWIS sends
 * synchronously (no Messenger queue).
 */
final class SentMailRegistry
{
    /** @var array<string, true> */
    private array $messageIds = [];

    public function remember(?string $messageId): void
    {
        if (null !== $messageId && '' !== $messageId) {
            $this->messageIds[$this->normalize($messageId)] = true;
        }
    }

    public function knows(?string $messageId): bool
    {
        return null !== $messageId && isset($this->messageIds[$this->normalize($messageId)]);
    }

    /** Message-ID travels with angle brackets on the wire, but is stored without them. */
    private function normalize(string $messageId): string
    {
        return trim($messageId, '<> ');
    }
}
