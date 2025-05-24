<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class MailerSubscriber implements EventSubscriberInterface
{
    public function __construct(protected OswisCoreSettingsProvider $coreSettings)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [MessageEvent::class => 'onMessageSend'];
    }

    public function onMessageSend(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (!($email instanceof Email)) {
            return;
        }
        $this->processFromAddresses($email);
        $this->processRecipients($email);
        $coreEmailSettings = $this->coreSettings->getEmail();
        if ($email->getReturnPath() ?? $coreEmailSettings['return_path'] ?? null) {
            $email->returnPath($email->getReturnPath() ?? $coreEmailSettings['return_path'] ?? '');
        }
        if ($email->getReplyTo()[0] ?? $coreEmailSettings['reply_path'] ?? null) {
            $email->replyTo($email->getReplyTo()[0] ?? $coreEmailSettings['reply_path'] ?? '');
        }
        $email->subject($email->getSubject() ?? $coreEmailSettings['default_subject'] ?? '');
        // if ($email instanceof TemplatedEmail) {
        // $email->embedFromPath('../assets/images/logo.png', 'logo');
        // $email->getContext()['logo'] = $email->getContext()['logo'] ?? 'cid:logo';
        // $email->getContext()['oswis'] = $this->coreSettings->getArray();
        // }
        $event->setMessage($email);
    }

    private function processFromAddresses(Email $email): void
    {
        $coreEmailSettings = $this->coreSettings->getEmail();
        if (!empty($email->getFrom())) {
            $originalSenders = $email->getFrom();
            foreach ($originalSenders as $index => $singleFrom) {
                try {
                    if ($index < 1) {
                        $email->from(new Address($singleFrom->getAddress(), $singleFrom->getName()));
                    } else {
                        $email->addFrom(new Address($singleFrom->getAddress(), $singleFrom->getName()));
                    }
                } catch (LogicException|RfcComplianceException $e) {
                    $email->addFrom($singleFrom);
                }
            }
        }
        if (($coreEmailSettings['address'] ?? null) && empty($email->getFrom())) {
            $fromAddress = $coreEmailSettings['address'];
            $fromName = $coreEmailSettings['name'] ?? '';
            try {
                $email->from(new Address($fromAddress, $fromName));
            } catch (LogicException|RfcComplianceException $e) {
                $email->from($fromAddress);
            }
        }
    }

    private function processRecipients(Email $email): void
    {
        $originalRecipients = $email->getTo();
        $email->to();
        foreach ($originalRecipients as $singleTo) {
            try {
                $email->addTo(new Address($singleTo->getAddress(), $singleTo->getName()));
            } catch (LogicException|RfcComplianceException) {
                $email->addTo($singleTo->getAddress());
            }
        }
        try {
            $archiveAddress = $this->coreSettings->getArchiveMailerAddress();
            if ($archiveAddress) {
                $email->addBcc($archiveAddress);
            }
        } catch (RfcComplianceException|LogicException) {
        }
    }
}
