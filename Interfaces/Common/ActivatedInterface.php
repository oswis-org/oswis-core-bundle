<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface ActivatedInterface
{
    public function isActivated(?DateTime $dateTime = null): bool;

    public function getActivated(): ?DateTime;

    public function setActivated(?DateTime $activated): void;

    public function activate(?DateTime $dateTime = null): void;
}
