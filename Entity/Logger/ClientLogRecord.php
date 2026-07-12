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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Log record from client.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['entities_post', 'client_log_records_post']],
            // Hardening: dřív bez security = kdokoli NEPŘIHLÁŠENÝ mohl neomezeně
            // plnit tabulku core_client_log_record (DoS / vyčerpání úložiště).
            // Endpoint nikdo nevolá (0 záznamů, grep 0 v Ionic i web); klientský
            // log dává smysl jen pro přihlášeného klienta. Operační security se
            // na stateless /api firewallu vynutí (na rozdíl od access_control).
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
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
    #[Assert\Length(max: 255)] // odpovídá VARCHAR(255) (Doctrine default) → žádná schema změna; brání truncation/DB erroru
    public ?string $message = null;

    public function __construct(
        ?int $level = null,
        ?string $message = null
    ) {
        $this->level = $level;
        $this->message = $message;
    }
}
