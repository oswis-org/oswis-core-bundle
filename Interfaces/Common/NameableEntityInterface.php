<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

interface NameableEntityInterface extends BasicEntityInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getSlug(): ?string;

    public function setSlug(?string $slug): ?string;

    public function getForcedSlug(): ?string;

    public function setForcedSlug(?string $forcedSlug): void;

    public function getShortName(): ?string;

    public function setShortName(?string $shortName): void;

    public function getDescription(): string;

    public function setDescription(?string $description): void;

    public function getNote(): ?string;

    public function setNote(?string $note): void;

    public function getInternalNote(): ?string;

    public function setInternalNote(?string $internalNote): void;
}
