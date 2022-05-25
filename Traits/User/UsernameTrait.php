<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds username field.
 */
trait UsernameTrait
{
    /** Username. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(name: 'username', type: 'string', length: 50, unique: true, nullable: true)]
    protected ?string $username = null;

    /** Get username. */
    public function getUsername(): ?string
    {
        return $this->username ?? null;
    }

    /** Set username. */
    public function setUsername(?string $username): void
    {
        $this->username = $username ?? null;
    }
}
