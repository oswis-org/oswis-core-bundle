<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;

class MailService
{
    public function __construct(
        protected LoggerInterface $logger,
        protected MailerInterface $mailer,
        protected EntityManagerInterface $em,
        protected BodyRendererInterface $bodyRenderer,
    ) {
    }

    public function sendEMail(AbstractMail $eMail, string $template, array $data = []): void
    {
        $this->em->persist($eMail);
        $class = get_class($eMail);
        try {
            $mail = $eMail->getTemplatedEmail()->htmlTemplate($template)->context($data);
            // Render now — transport-independent (works even if mail later goes async
            // via Messenger, where the mailer listener would otherwise render in the
            // worker) — so we can persist exactly what we deliver for the admin timeline.
            $this->bodyRenderer->render($mail);
            if (is_string($renderedHtml = $mail->getHtmlBody())) {
                $eMail->setBodyHtml($renderedHtml);
            }
            if (is_string($renderedText = $mail->getTextBody())) {
                $eMail->setBody($renderedText);
            }
            $this->mailer->send($mail);
        } catch (Exception|TransportExceptionInterface $exception) {
            $this->logger->error("E-mail ($class) NOT sent: ".$exception->getMessage());
            $eMail->setStatusMessage($exception->getMessage());
            $this->persistState($eMail, $class);

            return;
        }
        // Od tohoto bodu je zpráva FYZICKY doručena mailerem. Cokoli se pokazí dál se už nedá vzít
        // zpět — jde jen o to, aby se o tom vědělo a aby to neshodilo zbytek běhu.
        $eMail->setSent(new DateTime());
        try {
            $this->em->flush();
        } catch (Exception $exception) {
            // Neúspěšný commit zavře EntityManager (`UnitOfWork::commit()` → `em->close()` ve `finally`),
            // takže sloupec `sent` v DB nikdy nebude. Dotazy na „neodmailované" příjemce ho tedy dál
            // vidí a příští běh cronu mu pošle TÝŽ e-mail znovu.
            $this->logger->critical(
                "E-mail ($class) BYL odeslán, ale zápis o odeslání selhal — hrozí duplicitní odeslání: "
                .$exception->getMessage()
            );
            $eMail->setStatusMessage($exception->getMessage());
            $this->persistState($eMail, $class);

            return;
        }
        $id = $eMail->getId();
        $messageID = $eMail->getMessageID();
        $this->logger->info("E-mail ($class) sent with ID '$id' and Message-ID '$messageID'.");
    }

    /**
     * Zápis stavu e-mailu po chybě. Na zavřeném EntityManageru by každý `flush()` hodil
     * `EntityManagerClosed`; dřív se flushovalo naslepo a ta druhá výjimka utekla ze `sendEMail()`
     * nezachycená — původní chyba se nikdy nezalogovala a celý cron / request spadl na prvním
     * vadném e-mailu.
     */
    private function persistState(AbstractMail $eMail, string $class): void
    {
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
