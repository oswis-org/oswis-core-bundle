<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\EventSubscriber;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Exception\RfcComplianceException;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;

class MailerSubscriber implements EventSubscriberInterface
{
    protected OswisCoreSettingsProvider $coreSettings;

    public function __construct(OswisCoreSettingsProvider $oswisCoreSettings)
    {
        $this->coreSettings = $oswisCoreSettings;
    }

    public static function getSubscribedEvents(): array
    {
        return [MessageEvent::class => ['onMessageSend', 0]];
    }

    public function onMessageSend(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (!($email instanceof Email)) {
            return;
        }
        $this->processFromAddresses($email);
        $this->processRecipients($email);
        if ($email->getReturnPath() ?? $this->coreSettings->getEmail()['return_path']) {
            $email->returnPath($email->getReturnPath() ?? $this->coreSettings->getEmail()['return_path']);
        }
        if ($email->getReplyTo()[0] ?? $this->coreSettings->getEmail()['reply_path']) {
            $email->replyTo($email->getReplyTo()[0] ?? $this->coreSettings->getEmail()['reply_path']);
        }
        $email->subject(self::mimeEnc($email->getSubject() ?? $this->coreSettings->getEmail()['default_subject'] ?? ''));
        if ($email instanceof TemplatedEmail) {
            $email->embedFromPath('../assets/assets/images/logo.png', 'logo');
            $email->getContext()['logo'] = $email->getContext()['logo'] ?? 'cid:logo';
            $email->getContext()['oswis'] = $this->coreSettings->getArray();
        }
        $event->setMessage($email);
    }

    private function processFromAddresses(Email $email): void
    {
        if (!empty($email->getFrom())) {
            $originalSenders = $email->getFrom();
            $email->from();
            foreach ($originalSenders as $singleFrom) {
                try {
                    $email->addFrom(new Address($singleFrom->getAddress(), self::mimeEnc($singleFrom->getName())));
                } catch (LogicException | RfcComplianceException $e) {
                    $email->addFrom($singleFrom);
                }
            }
        }
        if (empty($email->getFrom()) && $this->coreSettings->getEmail()['address']) {
            $fromAddress = $this->coreSettings->getEmail()['address'] ?? null;
            $fromName = self::mimeEnc($this->coreSettings->getEmail()['name'] ?? null);
            try {
                $email->from(new Address($fromAddress, $fromName));
            } catch (LogicException | RfcComplianceException $e) {
                $email->from($fromAddress);
            }
        }
    }

    private static function mimeEnc(string $value): string
    {
        return EmailUtils::mime_header_encode($value);
    }

    private function processRecipients(Email $email): void
    {
        $originalRecipients = $email->getTo();
        $email->to();
        foreach ($originalRecipients as $singleTo) {
            try {
                $email->addTo(new Address($singleTo->getAddress(), self::mimeEnc($singleTo->getName())));
            } catch (LogicException | RfcComplianceException $e) {
                $email->addTo($singleTo->getAddress());
            }
        }
    }
}
