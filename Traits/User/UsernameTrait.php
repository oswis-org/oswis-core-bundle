<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\User;

/**
 * Trait adds username field.
 */
trait UsernameTrait
{
    /**
     * Username.
     * @Doctrine\ORM\Mapping\Column(name="username", type="string", length=50, unique=true, nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $username = null;

    /**
     * Get username.
     */
    public function getUsername(): ?string
    {
        return $this->username ?? null;
    }

    /**
     * Set username.
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username ?? null;
    }
}
