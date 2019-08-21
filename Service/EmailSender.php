<?php

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use Psr\Log\LoggerInterface;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;

/**
 * Service for sending e-mails.
 */
class EmailSender
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Twig_Environment
     */
    protected $templating;

    /**
     * @var OswisCoreSettingsProvider
     */
    protected $oswisCoreSettings;

    /**
     * E-mail sender constructor.
     *
     * @param Swift_Mailer              $mailer
     * @param LoggerInterface           $logger
     * @param Environment               $templating
     * @param OswisCoreSettingsProvider $oswisCoreSettings
     */
    public function __construct(
        Swift_Mailer $mailer,
        LoggerInterface $logger,
        Environment $templating,
        OswisCoreSettingsProvider $oswisCoreSettings
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->templating = $templating;
        $this->oswisCoreSettings = $oswisCoreSettings;
    }

    /**
     * @param array       $recipients
     * @param string|null $title
     *
     * @param array       $sender
     *
     * @param string      $senderAccountEmail
     *
     * @return Swift_Message
     * @throws ErrorException
     */
    final public function getPreparedMessage(
        array $recipients,
        string $title = 'Systémová zpráva',
        array $sender = null,
        ?string $senderAccountEmail = null
    ): Swift_Message {
        try {
            $sender = $sender ?? array($this->oswisCoreSettings->getEmail()['address'] => EmailUtils::mime_header_encode($this->oswisCoreSettings->getEmail()['name']));
            $message = new Swift_Message(EmailUtils::mime_header_encode($title));
            $message->setTo($recipients);
            $message->setCharset('UTF-8');
            $message->setFrom($sender);
            $message->setSender($senderAccountEmail);

            return $message;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ErrorException('Problém s přípravou zprávy.  '.$e->getMessage());
        }
    }

    /**
     * @param Swift_Message $message
     * @param string        $templateName
     * @param array         $data
     * @param string        $logoPath
     *
     * @throws ErrorException
     */
    final public function sendMessage(
        Swift_Message $message,
        string $templateName = '@ZakjakubOswisCore/e-mail/message',
        array $data = [],
        string $logoPath = '@ZakjakubOswisCore/Resources/public/logo.png'
    ): void {
        try {
            /// TODO: Check template!!!
            $cidLogo = $message->embed(Swift_Image::fromPath($logoPath));
            $args = array(
                'title'        => $message->getSubject(),
                'logo'         => $cidLogo,
                'appNameShort' => 'OSWIS',
                'appNameLong'  => 'One Simple Web IS',
                'data'         => $data,
            );
            $message->setBody(
                $this->templating->render($templateName.'.html.twig', $args),
                'text/html'
            );
            $message->addPart(
                $this->templating->render($templateName.'.txt.twig', $args),
                'text/plain'
            );
            if ($this->mailer->send($message)) {
                return;
            }
            throw new ErrorException('Problém s odesláním zprávy.');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ErrorException('Problém s odesláním zprávy: '.$e->getMessage());
        }
    }
}
