<?php

namespace OswisOrg\OswisCoreBundle\Entity\Logger;

use ApiPlatform\Core\Annotation\ApiResource;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicEntityInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicEntityTrait;

/**
 * Log record from client.
 *
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="core_client_log_record")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *     "normalization_context"={"groups"={"client_log_records_get"}},
 *     "denormalization_context"={"groups"={"client_log_records_post"}}
 *   },
 *   collectionOperations={
 *     "post"={
 *       "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *       "denormalization_context"={"groups"={"client_log_records_post"}}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "denormalization_context"={"groups"={"client_log_record_get"}}
 *     }
 *   }
 * )
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_log")
 */
class ClientLogRecord implements BasicEntityInterface
{
    use BasicEntityTrait;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    public ?int $level = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    public ?string $message = null;

    public function __construct(
        ?int $level = null,
        ?string $message = null
    ) {
        $this->level = $level;
        $this->message = $message;
    }
}
