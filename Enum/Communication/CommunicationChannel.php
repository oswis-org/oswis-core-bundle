<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Enum\Communication;

/**
 * Channel through which the communication entry happened. Direction
 * (in/out) is tracked separately on CommunicationDirection — channel only
 * names the medium.
 *
 * - SYSTEM_MAIL: mail sent automatically by OSWIS (activation, summary, payment confirmation).
 * - AD_HOC_MAIL: mail composed manually by admin from OSWIS UI.
 * - IMAP_MAIL:   mail observed via IMAP (Inbox or Sent), regardless of who wrote it.
 * - PHONE:       phone call logged manually by admin.
 * - CHAT:        chat conversation snapshot logged manually by admin.
 *
 * `INCOMING_MAIL` is the historical name kept for the enum value to stay
 * backwards-compatible with stored data; its label is direction-agnostic.
 */
enum CommunicationChannel: string
{
    case SYSTEM_MAIL = 'system_mail';
    case AD_HOC_MAIL = 'ad_hoc_mail';
    case INCOMING_MAIL = 'incoming_mail';
    case PHONE = 'phone';
    case CHAT = 'chat';

    public function label(): string
    {
        return match ($this) {
            self::SYSTEM_MAIL   => 'Systémový e-mail',
            self::AD_HOC_MAIL   => 'E-mail (ad-hoc)',
            self::INCOMING_MAIL => 'E-mail (IMAP)',
            self::PHONE         => 'Telefonát',
            self::CHAT          => 'Chat',
        };
    }

    /**
     * Jméno ikony pro `<twig:ux:icon>`.
     *
     * ⚠️ Sada je `tabler` a JEN ta, protože ikony se sem nestahují ze sítě — leží
     * v `assets/icons/tabler/`. Dřív tu stály názvy z `mdi:`, které v projektu
     * nejsou: v prostředí `dev` má UX Icons `ignore_not_found: false`, takže
     * první použití téhle metody by shodilo stránku výjimkou, a na produkci by
     * se místo ikony nevykreslilo mlčky nic. Metoda se do 27. 8. 2026 nikde
     * nevolala, takže to nikdo nepotkal. Nové názvy vybírat z toho adresáře.
     */
    public function iconifyName(): string
    {
        return match ($this) {
            self::SYSTEM_MAIL   => 'tabler:device-desktop',
            self::AD_HOC_MAIL   => 'tabler:mail-plus',
            self::INCOMING_MAIL => 'tabler:mail',
            self::PHONE         => 'tabler:phone',
            self::CHAT          => 'tabler:message',
        };
    }
}
