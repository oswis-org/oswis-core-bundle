<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

/**
 * Log record from client.
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="client_log_record")
 * @ApiResource(
 *   attributes={
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
 *       "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *       "denormalization_context"={"groups"={"client_log_record_get"}}
 *     }
 *   }
 * )
 */
class ClientLogRecord
{
    use BasicEntityTrait;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    public $level;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    public $message;

    public function __construct(
        ?int $level = null,
        ?string $message = null
    ) {
        $this->level = $level;
        $this->message = $message;
    }

}
