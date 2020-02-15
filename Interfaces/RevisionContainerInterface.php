<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Entity\AbstractClass\AbstractRevision;

interface RevisionContainerInterface
{
    public function getRevision(DateTimeInterface $dateTime = null): AbstractRevision;

    public function getRevisions(): Collection;

    public function addRevision(?AbstractRevision $revision): void;

    public function removeRevision(?AbstractRevision $revision): void;
}
