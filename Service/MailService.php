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
            $eMail->setSent(new DateTime());
            $this->em->flush();
            $id = $eMail->getId();
            $messageID = $eMail->getMessageID();
            $this->logger->info("E-mail ($class) sent with ID '$id' and Message-ID '$messageID'.");
        } catch (Exception|TransportExceptionInterface $exception) {
            $eMail->setStatusMessage($exception->getMessage());
            $this->em->flush();
            $this->logger->error("E-mail ($class) NOT sent: ".$exception->getMessage());
        }
    }
}
