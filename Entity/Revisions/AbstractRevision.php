<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\Revisions;

use DateTime;
use OswisOrg\OswisCoreBundle\Interfaces\Revisions\RevisionInterface;
use OswisOrg\OswisCoreBundle\Utils\DateTimeUtils;

use function array_reverse;
use function usort;

/**
 * Abstract class for revision (version) of some entity (of some container which extends AbstractRevisionContainer).
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractRevision implements RevisionInterface
{
    /**
     * Function for (in-place) sorting of array of revisions by createdAt and id.
     */
    public static function sortByCreatedAt(array &$revisions): void
    {
        $revisions = array_reverse($revisions);
        usort($revisions, static function (self $arg1, self $arg2) {
            $cmpResult = DateTimeUtils::cmpDate($arg2->getCreatedAt(), $arg1->getCreatedAt());

            return 0 === $cmpResult ? self::cmpId($arg2->getId(), $arg1->getId()) : $cmpResult;
        });
    }

    /**
     * Date and time of revision creation.
     */
    abstract public function getCreatedAt(): ?DateTime;

    /**
     * Helper function for sorting by id of revisions.
     */
    public static function cmpId(?int $a, ?int $b): int
    {
        if ($a === $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    /**
     * ID of this revision (version).
     */
    abstract public function getId(): ?int;
}
