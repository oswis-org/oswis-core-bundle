<?php

namespace OswisOrg\OswisCoreBundle\Interfaces;

interface IdInterface
{
    public function getId(): ?int;

    public function setId(int $id): void;
}
