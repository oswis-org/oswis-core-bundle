<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\MailQuota;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Kolik e-mailů OSWIS za den odeslal (den v pražském čase) — podklad denního limitu
 * ({@see \OswisOrg\OswisCoreBundle\Mail\Quota\MailDailyQuota}, spec e-mailů §5.5).
 *
 * Počítají se ZPRÁVY (jedno odeslání = +1), ne adresy — skrytá kopie do archivu se nepočítá. Zapisuje se
 * syrovým `INSERT … ON DUPLICATE KEY UPDATE` (souběh cronu a administrace); entita je tu kvůli schématu.
 */
#[Entity]
#[Table(name: 'core_mail_send_counter')]
class MailSendCounter
{
    #[Id]
    #[Column(name: 'day', type: 'date_immutable')]
    private \DateTimeImmutable $day;

    #[Column(name: 'sent_count', type: 'integer', options: ['default' => 0])]
    private int $sentCount = 0;

    public function __construct(\DateTimeImmutable $day)
    {
        $this->day = $day;
    }

    public function getDay(): \DateTimeImmutable
    {
        return $this->day;
    }

    public function getSentCount(): int
    {
        return $this->sentCount;
    }
}
