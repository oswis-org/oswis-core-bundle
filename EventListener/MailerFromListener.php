<?php

namespace Zakjakub\OswisCoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\NamedAddress;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;

class MailerFromListener implements EventSubscriberInterface
{

    protected $oswisCoreSettings;

    public function __construct(OswisCoreSettingsProvider $oswisCoreSettings)
    {
        $this->oswisCoreSettings = $oswisCoreSettings;
    }


    final public static function getSubscribedEvents(): array
    {
        return [MessageEvent::class => 'onMessageSend'];
    }

    final public function onMessageSend(MessageEvent $event): void
    {
        $message = $event->getMessage();
        $oswisCoreSettings = $this->oswisCoreSettings;

        if (!$message instanceof Email) {
            return;
        }

        if (!$message->getFrom() && $oswisCoreSettings->getEmail()['address']) {
            $message->from(
                new NamedAddress(
                    EmailUtils::mime_header_encode($oswisCoreSettings->getEmail()['address'] ?? null),
                    EmailUtils::mime_header_encode($oswisCoreSettings->getEmail()['name'] ?? null)
                )
            );
        }
        if (!$message->getReturnPath() && $oswisCoreSettings->getEmail()['return_path']) {
            $message->returnPath($oswisCoreSettings->getEmail()['return_path']);
        }
        if (!$message->getReplyTo() && $oswisCoreSettings->getEmail()['reply_path']) {
            $message->replyTo($oswisCoreSettings->getEmail()['reply_path']);
        }
        if (!$message->getSubject() && $oswisCoreSettings->getEmail()['default_subject']) {
            $message->subject(EmailUtils::mime_header_encode($oswisCoreSettings->getEmail()['default_subject']));
        }
    }
}