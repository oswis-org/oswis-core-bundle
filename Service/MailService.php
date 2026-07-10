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
        $physicallySent = false;
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
            $physicallySent = true;
            $eMail->setSent(new DateTime());
            $this->em->flush();
            $id = $eMail->getId();
            $messageID = $eMail->getMessageID();
            $this->logger->info("E-mail ($class) sent with ID '$id' and Message-ID '$messageID'.");
        } catch (Exception|TransportExceptionInterface $exception) {
            $this->logger->error("E-mail ($class) NOT sent: ".$exception->getMessage());
            if ($physicallySent) {
                // The message left the mailer but the row saying so never hit the DB, so every
                // "unmailed" query still sees this recipient — the next cron run will send it again.
                // Nothing here can undo the delivery; make sure a human can find out.
                $this->logger->critical(
                    "E-mail ($class) BYL odeslán, ale zápis o odeslání selhal — hrozí duplicitní odeslání: "
                    .$exception->getMessage()
                );
            }
            $eMail->setStatusMessage($exception->getMessage());
            // A failed flush closes the EntityManager (UnitOfWork::commit() → em->close() in finally),
            // and every later flush()/persist() throws EntityManagerClosed. Flushing blindly here used
            // to let that secondary exception escape sendEMail() uncaught: the original error was never
            // logged and the whole cron/registration request died on the first bad mail.
            if (!$this->em->isOpen()) {
                $this->logger->error(
                    "E-mail ($class): EntityManager je zavřený, stav e-mailu se do DB nezapsal."
                );

                return;
            }
            try {
                $this->em->flush();
            } catch (Exception $flushException) {
                $this->logger->error(
                    "E-mail ($class): stav e-mailu se nepodařilo zapsat: ".$flushException->getMessage()
                );
            }
        }
    }
}
