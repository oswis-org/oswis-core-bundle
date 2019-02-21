<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Entity\AbstractRevision;

interface RevisionContainerInterface
{

    public function getRevision(\DateTime $dateTime = null): AbstractRevision;

    public function getRevisions(): Collection;

    public function addRevision(?AbstractRevision $revision): void;

    public function removeRevision(?AbstractRevision $revision): void;
}
