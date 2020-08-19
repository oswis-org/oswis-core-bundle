<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

interface ManagerConfirmationInterface
{
    public function getManagerConfirmedBy(): ?AppUser;

    public function setManagerConfirmedBy(?AppUser $appUser): void;

    public function getManagerConfirmedAt(): ?DateTime;

    public function setManagerConfirmedAt(?DateTime $dateTime): void;

    public function setManagerConfirmed(AppUser $appUser): void;
}
