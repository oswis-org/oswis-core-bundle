<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

interface UserConfirmationInterface
{
    public function getUserConfirmedBy(): ?AppUser;

    public function setUserConfirmedBy(?AppUser $appUser): void;

    public function getUserConfirmedAt(): ?DateTime;

    public function setUserConfirmedAt(?DateTime $dateTime): void;

    public function setUserConfirmed(AppUser $appUser): void;
}
