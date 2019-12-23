<?php

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
    protected OswisCoreSettingsProvider $oswisCoreSettings;

    public function __construct(OswisCoreSettingsProvider $oswisCoreSettings)
    {
        $this->oswisCoreSettings = $oswisCoreSettings;
    }

    final public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => ['onMessageSend', 0],
        ];
    }

    /**
     * @noinspection PhpUnused
     */
    final public function onMessageSend(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!($message instanceof Email)) {
            return;
        }
        $oswisCoreSettings = $this->oswisCoreSettings;
        if (!$message->getFrom() && $oswisCoreSettings->getEmail()['address']) {
            try {
                $fromAddress = $oswisCoreSettings->getEmail()['address'] ?? null;
                $fromName = EmailUtils::mime_header_encode($oswisCoreSettings->getEmail()['name'] ?? null);
                $message->from([new Address($fromAddress, $fromName)]);
            } catch (LogicException $e) {
                /// TODO: Catch.
            } catch (RfcComplianceException $e) {
                /// TODO: Catch.
            }
        }
        if (!$message->getReturnPath() && $oswisCoreSettings->getEmail()['return_path']) {
            $message->returnPath($oswisCoreSettings->getEmail()['return_path']);
        }
        if (!$message->getReplyTo() && $oswisCoreSettings->getEmail()['reply_path']) {
            $message->addReplyTo($oswisCoreSettings->getEmail()['reply_path']);
        }
        if ($message->getSubject()) {
            $message->subject(EmailUtils::mime_header_encode($message->getSubject()));
        }
        if (!$message->getSubject() && $oswisCoreSettings->getEmail()['default_subject']) {
            $message->subject(EmailUtils::mime_header_encode($oswisCoreSettings->getEmail()['default_subject']));
        }
        if ($message instanceof TemplatedEmail) {
            $message->embedFromPath('../assets/assets/images/logo.png', 'logo');
            if ($message->getContext()['logo']) {
                $message->getContext()['logo'] = 'cid:logo';
            }
            if ($message->getContext()['oswis']) {
                $message->getContext()['oswis'] = $oswisCoreSettings->getArray();
            }
            $event->setMessage($message);
        }
    }
}
