<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractEMail;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\LogicException as MimeLogicException;

class AbstractMailService
{
    protected EntityManagerInterface $em;

    protected LoggerInterface $logger;

    protected MailerInterface $mailer;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailerInterface $mailer
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * @param AbstractEMail $eMail
     * @param string        $template
     * @param array         $data
     * @param string|null   $fullName
     *
     * @throws TransportExceptionInterface|MimeLogicException
     */
    public function sendEMail(AbstractEMail $eMail, string $template, array $data = [], ?string $fullName = null): void
    {
        $mail = new TemplatedEmail();
        try {
            $mail->to(new Address($eMail->getEmail() ?? '', $fullName ?? ''));
        } catch (LogicException $e) {
            $mail->to($eMail->getEmail() ?? '');
        }
        $mail->subject($eMail->getName())->htmlTemplate($template)->context($data);
        $eMail->setCustomId(false, $mail->generateMessageId());
        $this->mailer->send($mail);
        $eMail->setSent(new DateTime());
    }
}
