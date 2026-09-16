<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\SystemMail;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail;

/**
 * Delivery of a mail that belongs to no person — a report for the team, a message caught by the
 * "nothing may leave unrecorded" guard, a test message.
 *
 * Without it, "every sent mail is stored" could not hold: the other delivery tables all require a
 * subject (a registration, an account), so mail like the payment-import report had nowhere to go
 * and went straight to the mailer instead, leaving no trace at all.
 */
#[Entity]
#[Table(name: 'core_system_mail')]
#[UniqueConstraint(name: 'uniq_system_mail_delivery_key', columns: ['delivery_key'])]
class SystemMail extends AbstractMail
{
}
