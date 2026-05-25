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

    public function iconifyName(): string
    {
        return match ($this) {
            self::SYSTEM_MAIL,
            self::AD_HOC_MAIL   => 'mdi:email-outline',
            self::INCOMING_MAIL => 'mdi:email-sync-outline',
            self::PHONE         => 'mdi:phone-outline',
            self::CHAT          => 'mdi:chat-outline',
        };
    }
}
