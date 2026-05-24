<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Enum\Communication;

/**
 * Channel through which the communication entry happened.
 *
 * - SYSTEM_MAIL: mail sent automatically by OSWIS (activation, summary, payment confirmation).
 * - AD_HOC_MAIL: mail composed manually by admin from OSWIS UI (Phase 4 / Komponenta C).
 * - INCOMING_MAIL: mail received from participant or third party, fetched via IMAP (Phase 6 / Komponenta D).
 * - PHONE: phone call logged manually by admin (Phase 3 / Komponenta B).
 * - CHAT: chat conversation snapshot logged manually by admin (Phase 3 / Komponenta B).
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
            self::INCOMING_MAIL => 'Příchozí e-mail',
            self::PHONE         => 'Telefonát',
            self::CHAT          => 'Chat',
        };
    }

    public function iconifyName(): string
    {
        return match ($this) {
            self::SYSTEM_MAIL,
            self::AD_HOC_MAIL   => 'mdi:email-outline',
            self::INCOMING_MAIL => 'mdi:email-arrow-left-outline',
            self::PHONE         => 'mdi:phone-outline',
            self::CHAT          => 'mdi:chat-outline',
        };
    }
}
