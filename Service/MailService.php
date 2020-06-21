<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use DateTime;
use Exception;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    protected LoggerInterface $logger;

    protected MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function sendEMail(AbstractMail $eMail, string $template, array $data = []): void
    {
        $class = get_class($eMail);
        try {
            $mail = $eMail->getTemplatedEmail()->htmlTemplate($template)->context($data);
            $this->mailer->send($mail);
            $eMail->setSent(new DateTime());
            $id = $eMail->getId();
            $messageID = $eMail->getMessageID();
            $this->logger->error("E-mail ($class) sent with ID '$id' and Message-ID '$messageID'.");
        } catch (Exception|TransportExceptionInterface $exception) {
            $eMail->setStatusMessage($exception->getMessage());
            $this->logger->error("E-mail ($class) NOT sent: ".$exception->getMessage());
        }
    }
}
