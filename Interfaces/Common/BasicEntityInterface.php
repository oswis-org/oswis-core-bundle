<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

interface BasicEntityInterface extends TimestampableInterface
{
    public function getId(): ?int;

    public function setId(int $id): void;

    public function getCustomId(): ?string;

    public function setCustomId(?bool $auto = true, ?string $customId = null): void;

    public function getCreatedAuthor(): ?AppUser;

    public function getUpdatedAuthor(): ?AppUser;

}
