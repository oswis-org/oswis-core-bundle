<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use Doctrine\DBAL\Connection;
use OswisOrg\OswisCoreBundle\Enum\Mail\MailDeliveryStatus;
use OswisOrg\OswisCoreBundle\Mail\Delivery\SentMailRegistry;
use OswisOrg\OswisCoreBundle\Mail\Secret\MailSecretRedactor;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\FailedMessageEvent;
use Symfony\Component\Mailer\Event\SentMessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Throwable;

/**
 * Pojistka „nic neodejde bez záznamu".
 *
 * Pravidlo, že poštu odesílá jediné místo, hlídá test nad zdrojovým kódem. Tenhle odběratel je
 * druhá vrstva pro to, co test nezachytí — cizí knihovnu, nový kód v aplikaci, cokoli, co si
 * mailer vezme napřímo. Zprávu bez záznamu zapíše jako systémový e-mail a nahlásí ji jako
 * kritickou chybu, aby se nezapomnělo, že takovou cestu je potřeba dodělat.
 *
 * Tělo se ukládá celé, jen bez míst označených jako tajná ({@see MailSecretRedactor}) — značka
 * v šabloně funguje i tady, kde o datech šablony nic nevíme.
 *
 * Zápis jde přímo přes databázové spojení, ne přes EntityManager: jeho `flush()` by uložil i to,
 * co má volající rozdělané, a odesílání pošty nesmí nikomu do práce mluvit.
 */
final readonly class UnrecordedMailGuard implements EventSubscriberInterface
{
    public function __construct(
        private SentMailRegistry $registry,
        private MailSecretRedactor $redactor,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SentMessageEvent::class   => 'onSent',
            FailedMessageEvent::class => 'onFailed',
        ];
    }

    public function onSent(SentMessageEvent $event): void
    {
        $message = $event->getMessage()->getOriginalMessage();
        $this->record($message, $this->messageIdOf($message), MailDeliveryStatus::SENT, null);
    }

    public function onFailed(FailedMessageEvent $event): void
    {
        $message = $event->getMessage();
        $this->record(
            $message,
            $this->messageIdOf($message),
            MailDeliveryStatus::FAILED,
            $event->getError()->getMessage(),
        );
    }

    /**
     * Identifikátor ZPRÁVY (hlavička Message-ID), ne identifikátor od poštovního serveru.
     *
     * `SentMessage::getMessageId()` vrací id přidělené SMTP serverem — jiné číslo, které v záznamu
     * nemáme. Podle něj pojistka hlásila jako „mimo službu" i e-maily, které službou prošly.
     */
    private function messageIdOf(object $message): ?string
    {
        return $message instanceof Message
            ? $message->getHeaders()->get('Message-ID')?->getBodyAsString()
            : null;
    }

    private function record(object $message, ?string $messageId, MailDeliveryStatus $status, ?string $reason): void
    {
        if (!$message instanceof Email || $this->registry->knows($messageId)) {
            return;
        }
        $recipient = $message->getTo()[0] ?? null;
        $this->logger->critical(sprintf(
            'E-mail odešel MIMO MailService (předmět „%s", příjemce %s) — zapsán jako systémový e-mail. '
            .'Tuhle cestu je potřeba převést na společnou službu.',
            $message->getSubject() ?? '',
            $recipient?->getAddress() ?? '?',
        ));
        try {
            $html = $message->getHtmlBody();
            $text = $message->getTextBody();
            $redactedHtml = is_string($html) ? $this->redactor->redactHtml($html) : null;
            $this->connection->insert('core_system_mail', [
                'type'           => 'unrecorded',
                'subject'        => $message->getSubject(),
                'address'        => $recipient?->getAddress(),
                'recipient_name' => $recipient?->getName(),
                'message_id'     => null === $messageId ? null : trim($messageId, '<> '),
                'status'         => $status->value,
                'attempt_count'  => 1,
                'status_message' => $reason,
                'sent'           => MailDeliveryStatus::SENT === $status ? date('Y-m-d H:i:s') : null,
                'body_html'      => $redactedHtml,
                'body'           => is_string($text) ? $this->redactor->redactValues($text, []) : null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $exception) {
            // Zápis pojistky nesmí shodit odeslání — zpráva už stejně odešla.
            $this->logger->error('Záznam o e-mailu mimo MailService se nepodařilo uložit: '.$exception->getMessage());
        }
    }
}
