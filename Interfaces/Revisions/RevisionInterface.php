<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Revisions;

use OswisOrg\OswisCoreBundle\Entity\Revisions\AbstractRevisionContainer;

interface RevisionInterface
{
    public static function sortByCreatedDateTime(array &$revisions): void;

    // public function hasSameValues(AbstractRevision $revision): bool;
    public function getContainer(): ?AbstractRevisionContainer;

    public function setContainer(?AbstractRevisionContainer $container): void;
}
