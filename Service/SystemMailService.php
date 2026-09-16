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
    ): SystemMail {
        $mail = new SystemMail($subject, $address, $type, $recipientName);
        if (null !== $deliveryKey) {
            $mail->setDeliveryKey($deliveryKey);
        }
        $this->em->persist($mail);
        $this->mailService->sendEMail($mail, $template, $data);

        return $mail;
    }
}
