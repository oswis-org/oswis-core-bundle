<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Enum\Communication;

/**
 * Direction of a communication entry relative to OSWIS / our team.
 *
 * - OUT: we sent something to the participant (system mail, admin ad-hoc mail, team-initiated call).
 * - IN: participant contacted us (incoming mail, returned call).
 * - INTERNAL: team-internal note about the participant; not communication with them.
 */
enum CommunicationDirection: string
{
    case OUT = 'out';
    case IN = 'in';
    case INTERNAL = 'internal';

    public function label(): string
    {
        return match ($this) {
            self::OUT      => 'Odchozí',
            self::IN       => 'Příchozí',
            self::INTERNAL => 'Interní',
        };
    }
}
