<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

interface BasicInterface extends TimestampableInterface
{
    public function getId(): ?int;

    public function setId(int $id): void;

    public function getCustomId(): ?string;

    public function setCustomId(?bool $auto = true, ?string $customId = null): void;

    public function getCreatedBy(): ?AppUser;

    public function getUpdatedBy(): ?AppUser;
}
