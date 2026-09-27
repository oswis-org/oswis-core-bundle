<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Message;

/**
 * Podpis DKIM odchozí pošty (konfigurace `oswis_org_oswis_core.email.dkim`).
 *
 * PROČ: od 27. 9. 2026 se odesílá z vlastního serveru (Postfix) místo SMTP hostingu. Podpis
 * v aplikaci (ne na serveru): ISPConfig při každém uložení přepíše mapu podpisových domén rspamd
 * a doména s poštou jinde (MX u Webglobe) tam být nemá. DMARC domény pak projde přes DKIM
 * nezávisle na SPF (i při přeposlání).
 *
 * Proti vestavěnému `framework.mailer.dkim_signer` dvě pojistky:
 *  - chybějící nebo nečitelný klíč NESMÍ zastavit poštu → mail odejde nepodepsaný, chyba do logu;
 *  - podepisuje se jen mail, jehož From je z domény podpisu (jinak by podpis nebyl zarovnaný
 *    s DMARC a nic by nepřinesl).
 *
 * Priorita −128 = úplně nakonec (jako Symfony): podpis musí vzniknout až nad hotovou zprávou,
 * po {@see MailerSubscriber} (odesílatel, archivní BCC, hlavičky).
 */
final class MailDkimSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly OswisCoreSettingsProvider $coreSettings,
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [MessageEvent::class => ['onMessage', -128]];
    }

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof Message) {
            return;
        }
        $nastaveni = $this->coreSettings->getEmail();
        $dkim = $nastaveni['dkim'] ?? [];
        $klic = trim((string) ($dkim['key_path'] ?? ''));
        if ('' === $klic) {
            return;
        }
        $domena = strtolower(trim((string) ($dkim['domain'] ?? '')));
        if ('' === $domena) {
            $domena = self::domena($nastaveni['address'] ?? '');
        }
        $selektor = trim((string) ($dkim['selector'] ?? '')) ?: 'oswis1';
        $from = $message->getHeaders()->get('From');
        $odesilatel = $from instanceof MailboxListHeader ? ($from->getAddresses()[0] ?? null)?->getAddress() : null;
        if ('' === $domena || null === $odesilatel || $domena !== self::domena($odesilatel)) {
            return;
        }
        try {
            if (!is_readable($klic)) {
                throw new \RuntimeException(sprintf('klíč „%s" neexistuje nebo ho nejde přečíst', $klic));
            }
            $event->setMessage((new DkimSigner('file://'.$klic, $domena, $selektor))->sign($message));
        } catch (\Throwable $chyba) {
            $this->logger->error('DKIM: e-mail odejde NEPODEPSANÝ — '.$chyba->getMessage());
        }
    }

    private static function domena(string $adresa): string
    {
        $zavinac = strrpos($adresa, '@');

        return false === $zavinac ? '' : strtolower(substr($adresa, $zavinac + 1));
    }
}
