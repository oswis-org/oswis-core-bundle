<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\Logger;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;

/**
 * Log record from client.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['entities_post', 'client_log_records_post']],
        ),
        new Get(
            normalizationContext: ['groups' => ['entities_post', 'client_log_record_get']],
            security: "is_granted('ROLE_MANAGER')",
        ),
    ],
    normalizationContext: ['groups' => ['entities_get', 'client_log_records_get']],
    denormalizationContext: ['groups' => ['entities_post', 'client_log_records_post']],
)]
#[Entity]
#[Table(name: 'core_client_log_record')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_log')]
class ClientLogRecord implements BasicInterface
{
    use BasicTrait;

    #[Column(type: 'integer', nullable: true)]
    public ?int $level = null;

    #[Column(type: 'string', nullable: true)]
    public ?string $message = null;

    public function __construct(
        ?int $level = null,
        ?string $message = null
    ) {
        $this->level = $level;
        $this->message = $message;
    }
}
