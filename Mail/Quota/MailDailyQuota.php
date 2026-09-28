<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Quota;

use Doctrine\DBAL\Connection;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

/**
 * Denní limit odeslaných e-mailů (spec e-mailů §5.5, rozhodnutí uživatele 28. 9. 2026).
 *
 * - **tvrdý limit** (výchozí 1000): nad ním neodejde nic — pojistka proti nehodě (dvojí rozeslání,
 *   zacyklená automatika), ne běžný provoz;
 * - **limit hromadných** (výchozí 800): hromadné zprávy a automaily se na něm zastaví a pokračují další den
 *   (kurzor zůstane); zbytek do tvrdého limitu je rezerva pro systémové maily (registrace, platby).
 *
 * Hodnoty jsou v prostředí (`OSWIS_MAIL_DAILY_LIMIT`, `OSWIS_MAIL_DAILY_BULK_LIMIT`) — navýšení = změna
 * proměnné, bez nasazení. **0 = bez limitu.** Den se počítá výslovně v Europe/Prague: `bin/console` i web si
 * pražský čas nastavují samy (od 25. 5. 2026), ale holé CLI na serveru má UTC — „dnes" tak nezávisí na tom,
 * odkud se služba zavolá.
 *
 * Chyba databáze počítadla nesmí zastavit poštu: `record()` ji jen zaloguje, `sentToday()` pak vrátí 0.
 */
final class MailDailyQuota
{
    public const string ZONE = 'Europe/Prague';

    public function __construct(
        private readonly Connection $connection,
        private readonly ClockInterface $clock,
        private readonly LoggerInterface $logger,
        private readonly int $hardLimit = 1000,
        private readonly int $bulkLimit = 800,
    ) {
    }

    /** Dnešní den v Praze (Y-m-d). */
    public function today(): string
    {
        return $this->clock->now()->setTimezone(new \DateTimeZone(self::ZONE))->format('Y-m-d');
    }

    public function sentToday(): int
    {
        try {
            $count = $this->connection->fetchOne('SELECT sent_count FROM core_mail_send_counter WHERE day = ?', [$this->today()]);
        } catch (\Throwable $exception) {
            $this->logger->error('Denní limit pošty: počítadlo nejde přečíst — '.$exception->getMessage());

            return 0;
        }

        return is_numeric($count) ? (int) $count : 0;
    }

    /** Jedna odeslaná zpráva. Volá {@see \OswisOrg\OswisCoreBundle\Service\MailService} po odeslání. */
    public function record(): void
    {
        try {
            $this->connection->executeStatement(
                'INSERT INTO core_mail_send_counter (day, sent_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE sent_count = sent_count + 1',
                [$this->today()],
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Denní limit pošty: odeslání se nezapočítalo — '.$exception->getMessage());
        }
    }

    public function hardLimit(): int
    {
        return max(0, $this->hardLimit);
    }

    public function bulkLimit(): int
    {
        return max(0, $this->bulkLimit);
    }

    /** Smí dnes odejít systémový mail (registrace, platba)? */
    public function systemAllowed(): bool
    {
        return 0 === $this->hardLimit() || $this->sentToday() < $this->hardLimit();
    }

    /** Smí dnes odejít další zpráva hromadné rozesílky / automailu? */
    public function bulkAllowed(): bool
    {
        $remaining = $this->remainingForBulk();

        return null === $remaining || $remaining > 0;
    }

    /** Kolik zpráv dnes ještě smí odejít hromadně (null = bez limitu). */
    public function remainingForBulk(): ?int
    {
        $limits = array_filter([$this->bulkLimit(), $this->hardLimit()], static fn (int $limit): bool => $limit > 0);
        if ([] === $limits) {
            return null;
        }

        return max(0, min($limits) - $this->sentToday());
    }
}
