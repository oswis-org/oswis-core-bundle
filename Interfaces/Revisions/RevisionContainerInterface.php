<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Revisions;

use DateTime;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\Revisions\AbstractRevision;

interface RevisionContainerInterface
{
    public function getRevision(DateTime $dateTime = null): AbstractRevision;

    public function getRevisions(): Collection;

    public function addRevision(?AbstractRevision $revision): void;

    public function removeRevision(?AbstractRevision $revision): void;
}
