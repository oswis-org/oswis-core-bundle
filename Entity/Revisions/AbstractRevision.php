<?php

namespace OswisOrg\OswisCoreBundle\Entity\Revisions;

use DateTime;
use OswisOrg\OswisCoreBundle\Exceptions\RevisionMissingException;
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
     * Container of revisions.
     */
    protected ?AbstractRevisionContainer $container = null;

    /**
     * Class name of revisions container.
     */
    abstract public static function getRevisionContainerClassName(): string;

    /**
     * Function for (in-place) sorting of array of revisions by createdDateTime and id.
     */
    public static function sortByCreatedDateTime(array &$revisions): void
    {
        $revisions = array_reverse($revisions);
        usort(
            $revisions,
            static function (self $arg1, self $arg2) {
                $cmpResult = DateTimeUtils::cmpDate($arg2->getCreatedDateTime(), $arg1->getCreatedDateTime());

                return 0 === $cmpResult ? self::cmpId($arg2->getId(), $arg1->getId()) : $cmpResult;
            }
        );
    }

    /**
     * Date and time of revision creation.
     */
    abstract public function getCreatedDateTime(): ?DateTime;

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

    /**
     * Container of this revision.
     */
    final public function getContainer(): ?AbstractRevisionContainer
    {
        static::checkRevisionContainer($this->container);

        return $this->container;
    }

    /**
     * Set container of this revision.
     */
    final public function setContainer(?AbstractRevisionContainer $container): void
    {
        static::checkRevisionContainer($container);
        if ($this->container && $this->container !== $container) {
            $this->container->removeRevision($this);
        }
        $this->container = $container;
        if ($container && $container !== $this->container) {
            $container->addRevision($this);
        }
    }

    /**
     * Check validity of container (ie. for check before setting container).
     */
    abstract public static function checkRevisionContainer(?AbstractRevisionContainer $revision): void;

    /**
     * Check if this revision is actual/active in specified datetime (or now if datetime is not specified).
     */
    final public function isActive(?DateTime $dateTime = null): bool
    {
        try {
            return $this === $this->container->getRevision($dateTime);
        } catch (RevisionMissingException $e) {
            return false;
        }
    }
}
