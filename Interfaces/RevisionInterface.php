<?php

namespace OswisOrg\OswisCoreBundle\Interfaces;

use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractRevisionContainer;

interface RevisionInterface
{
    public static function sortByCreatedDateTime(array &$revisions): void;

    // public function hasSameValues(AbstractRevision $revision): bool;
    public function getContainer(): ?AbstractRevisionContainer;

    public function setContainer(?AbstractRevisionContainer $container): void;
}
