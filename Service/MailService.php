<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail;
use OswisOrg\OswisCoreBundle\Mail\Delivery\SentMailRegistry;
use OswisOrg\OswisCoreBundle\Mail\Secret\MailSecretRedactor;
use OswisOrg\OswisCoreBundle\Mailer\CistyTextKonvertor;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;
use Throwable;

/**
 * The only place in OSWIS that hands a message to SMTP.
 *
 * Order of steps (do not reorder — each one exists because of a real incident):
 *   1. render the message,
 *   2. store its copy WITHOUT the marked secrets ({@see MailSecretRedactor}),
 *   3. write the delivery as "sending" and COMMIT it — only then send,
 *   4. write the result (sent / failed).
 *
 * Step 3 is the transactional-outbox order. Before it, the delivery was written only after a
 * successful send, so a failed write left no trace and the next run sent the same message again
 * — 17 duplicate payment confirmations on 2026-08-21. Now a crash between sending and writing
 * the result leaves the record in "sending", which no automatic sender ever repeats; a human
 * checks the archive mailbox and decides. And a write that fails BEFORE sending means nothing
 * is sent at all, which is the safer half of the trade: no mail about a change that was not saved.
 *
 * Transport failures never throw out of here — callers ask the returned delivery
 * ({@see AbstractMail::isSent()}); "it did not throw" is not proof of delivery.
 */
class MailService
{
    public function __construct(
        protected LoggerInterface $logger,
        protected MailerInterface $mailer,
        protected EntityManagerInterface $em,
        protected BodyRendererInterface $bodyRenderer,
        protected MailSecretRedactor $secretRedactor,
        protected CistyTextKonvertor $plainTextConverter,
        protected SentMailRegistry $sentMailRegistry,
    ) {
    }

    /**
     * @param array<string, mixed> $data template context
     *
     * @return AbstractMail the delivery record; its status is the only proof of what happened
     */
    public function sendEMail(AbstractMail $eMail, string $template, array $data = []): AbstractMail
    {
        $this->em->persist($eMail);
        $class = get_class($eMail);
        try {
            $mail = $eMail->getTemplatedEmail()->htmlTemplate($template)->context($data);
            // Render now — transport-independent (works even if mail later goes async
            // via Messenger, where the mailer listener would otherwise render in the
            // worker) — so we can persist exactly what we deliver for the admin timeline.
            $this->bodyRenderer->render($mail);
            $this->storeRenderedBody($eMail, $mail, $data);
        } catch (Throwable $exception) {
            $this->logger->error("E-mail ($class) NOT rendered: ".$exception->getMessage());
            $this->markFailed($eMail, $class, $exception->getMessage());

            return $eMail;
        }
        try {
            $eMail->markSending();
            $this->em->flush();
        } catch (UniqueConstraintViolationException $exception) {
            // Klíč jedinečnosti: totéž už někdo odeslal (druhý běh cronu, tlačítko v administraci).
            // Není to chyba — je to přesně ta pojistka, kvůli které klíč existuje.
            $this->logger->info(sprintf(
                'E-mail (%s) se neodeslal podruhé — klíč „%s" už v databázi je.',
                $class,
                (string) $eMail->getDeliveryKey(),
            ));

            return $eMail;
        } catch (Throwable $exception) {
            // Fail closed: without a committed record we do not send at all, otherwise nobody
            // would know the message went out (and the change it announces may not be saved either).
            $this->logger->error(
                "E-mail ($class) NEODESLÁN — zápis před odesláním selhal: ".$exception->getMessage()
            );

            return $eMail;
        }
        // Pojistka ({@see UnrecordedMailGuard}) se po odeslání ptá, jestli měla zpráva záznam —
        // zapsat se tedy musí DŘÍV, než odejde.
        $this->sentMailRegistry->remember($eMail->getMessageID());
        try {
            $this->mailer->send($mail);
        } catch (Exception|TransportExceptionInterface $exception) {
            $this->logger->error("E-mail ($class) NOT sent: ".$exception->getMessage());
            $this->markFailed($eMail, $class, $exception->getMessage());

            return $eMail;
        }
        // Od tohoto bodu je zpráva FYZICKY doručena mailerem. Cokoli se pokazí dál se už nedá vzít
        // zpět — jde jen o to, aby se o tom vědělo a aby to neshodilo zbytek běhu.
        $eMail->markSent();
        try {
            $this->em->flush();
        } catch (Exception $exception) {
            // Neúspěšný commit zavře EntityManager (`UnitOfWork::commit()` → `em->close()` ve `finally`),
            // takže sloupec `sent` v DB nikdy nebude — záznam zůstane ve stavu „odesílá se". Ten se
            // NIKDY neopakuje automaticky (viz `MailDeliveryStatus::blocksAutomaticResend()`), takže
            // druhé odeslání téhož už nehrozí; tým ho uvidí jako „nejistý" a ověří v archivu.
            $this->logger->critical(
                "E-mail ($class) BYL odeslán, ale zápis o odeslání selhal — zůstává jako nejistý: "
                .$exception->getMessage()
            );

            return $eMail;
        }
        $id = $eMail->getId();
        $messageID = $eMail->getMessageID();
        $this->logger->info("E-mail ($class) sent with ID '$id' and Message-ID '$messageID'.");

        return $eMail;
    }

    /**
     * Stores the copy shown in the communication history: the whole body, minus the parts the
     * template marked as secret. The message that leaves is untouched.
     *
     * The stored plain text is derived from the ALREADY redacted HTML (same converter the mailer
     * uses), so a password cannot survive in the text alternative.
     *
     * @param array<string, mixed> $data
     */
    private function storeRenderedBody(AbstractMail $eMail, TemplatedEmail $mail, array $data): void
    {
        $secrets = $this->secretRedactor->collectSecrets($data);
        $renderedHtml = $mail->getHtmlBody();
        $redactedHtml = null;
        if (is_string($renderedHtml)) {
            $redactedHtml = $this->secretRedactor->redactHtml($renderedHtml, $secrets);
            $eMail->setBodyHtml($redactedHtml);
        }
        $renderedText = $mail->getTextBody();
        if (is_string($redactedHtml)) {
            $eMail->setBody($this->plainTextConverter->convert($redactedHtml, 'utf-8'));

            return;
        }
        if (is_string($renderedText)) {
            $eMail->setBody($this->secretRedactor->redactValues($renderedText, $secrets));
        }
    }

    /**
     * Zápis stavu e-mailu po chybě. Na zavřeném EntityManageru by každý `flush()` hodil
     * `EntityManagerClosed`; dřív se flushovalo naslepo a ta druhá výjimka utekla ze `sendEMail()`
     * nezachycená — původní chyba se nikdy nezalogovala a celý cron / request spadl na prvním
     * vadném e-mailu.
     */
    private function markFailed(AbstractMail $eMail, string $class, string $reason): void
    {
        try {
            $eMail->markFailed($reason);
        } catch (Throwable $exception) {
            $this->logger->error("E-mail ($class): stav selhání nešlo nastavit: ".$exception->getMessage());
            $eMail->setStatusMessage($reason);
        }
        if (!$this->em->isOpen()) {
            $this->logger->error("E-mail ($class): EntityManager je zavřený, stav se do DB nezapsal.");

            return;
        }
        try {
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logger->error("E-mail ($class): stav se nepodařilo zapsat: ".$exception->getMessage());
        }
    }
}
