<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\SystemMail\SystemMail;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;

/**
 * Mails that belong to no person: reports for the team, messages caught by the guard, test sends.
 *
 * Same path as every other mail ({@see MailService}), so such a message is stored, has a delivery
 * state and counts towards the daily limit.
 */
class SystemMailService
{
    public function __construct(
        protected MailService $mailService,
        protected EntityManagerInterface $em,
    ) {
    }

    /**
     * @param array<string, mixed> $data     template context
     * @param string|null          $deliveryKey idempotency key for an automatic send ({@see \OswisOrg\OswisCoreBundle\Mail\Delivery\DeliveryKey})
     * @param bool                 $archiveCopy false = bez skryté kopie do archivu (zkouška sobě)
     * @param bool                 $manual      ručně poslaná zpráva (Auto-Submitted: no)
     * @param list<\OswisOrg\OswisCoreBundle\Mail\Attachment\AttachedFile> $attachments přiložené soubory
     *
     * @throws InvalidTypeException
     */
    public function send(
        string $type,
        string $address,
        string $subject,
        string $template,
        array $data = [],
        ?string $deliveryKey = null,
        ?string $recipientName = null,
        bool $archiveCopy = true,
        bool $manual = false,
        array $attachments = [],
    ): SystemMail {
        $mail = new SystemMail($subject, $address, $type, $recipientName);
        if (null !== $deliveryKey) {
            $mail->setDeliveryKey($deliveryKey);
        }
        if (!$archiveCopy) {
            $mail->withoutArchiveCopy();
        }
        if ($manual) {
            $mail->markAsManual();
        }
        // `persist()` dělá `MailService` — až po kontrole klíče jedinečnosti, aby se záznam,
        // který se nemá poslat podruhé, vůbec nedostal do jednotky práce.
        // Vrátit záznam, který se SKUTEČNĚ použil — při opakování nebo duplicitě je to ten starší.
        $zaznam = $this->mailService->sendEMail($mail, $template, $data, $attachments);

        return $zaznam instanceof SystemMail ? $zaznam : $mail;
    }
}
