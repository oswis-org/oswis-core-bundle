<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface DeletedInterface
{
    public function getDeletedDaysAgo(): ?int;

    public function delete(?DateTime $dateTime = null): void;

    public function getDeletedAt(): ?DateTime;

    public function setDeletedAt(?DateTime $deletedAt = null): void;

    public function isDeleted(?DateTime $referenceDateTime = null): bool;
}
