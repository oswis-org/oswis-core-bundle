<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\AddressBook;

interface PersonInterface extends ContactInterface
{
    public function getGivenName(): ?string;

    public function setGivenName(?string $givenName): void;

    public function getFamilyName(): ?string;

    public function setFamilyName(?string $familyName): void;

    public function getHonorificPrefix(): ?string;

    public function setHonorificPrefix(?string $honorificSuffix): void;

    public function getHonorificSuffix(): ?string;

    public function setHonorificSuffix(?string $honorificSuffix): void;

    public function getAdditionalName(): ?string;

    public function setAdditionalName(?string $middleName): void;

    public function getNickname(): ?string;

    public function setNickname(?string $nickname): void;
}
