<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

interface PriorityInterface
{
    public function getPriority(): ?int;

    public function setPriority(?int $priority): void;
}
