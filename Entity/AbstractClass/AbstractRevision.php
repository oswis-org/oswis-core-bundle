<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionInterface;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;
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
     * @var AbstractRevisionContainer|null
     */
    protected ?AbstractRevisionContainer $container;

    /**
     * Class name of revisions container.
     * @return string
     */
    abstract public static function getRevisionContainerClassName(): string;

    /**
     * Function for (in-place) sorting of array of revisions by createdDateTime and id.
     *
     * @param array $revisions
     */
    public static function sortByCreatedDateTime(array &$revisions): void
    {
        $revisions = array_reverse($revisions);
        usort(
            $revisions,
            static function (self $arg1, self $arg2) {
                $cmpResult = DateTimeUtils::cmpDate($arg2->getCreatedDateTime(), $arg1->getCreatedDateTime());

                return $cmpResult === 0 ? self::cmpId($arg2->getId(), $arg1->getId()) : $cmpResult;
            }
        );
    }

    /**
     * Helper function for sorting by id of revisions.
     *
     * @param int|null $a
     * @param int|null $b
     *
     * @return int
     */
    public static function cmpId(?int $a, ?int $b): int
    {
        if ($a === $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    /**
     * Check validity of container (ie. for check before setting container).
     *
     * @param AbstractRevisionContainer|null $revision
     */
    abstract public static function checkRevisionContainer(?AbstractRevisionContainer $revision): void;

    /**
     * Date and time of revision creation.
     * @return DateTime|null
     */
    abstract public function getCreatedDateTime(): ?DateTime;

    /**
     * ID of this revision (version).
     * @return int|null
     */
    abstract public function getId(): ?int;

    /**
     * Container of this revision.
     * @return AbstractRevisionContainer|null
     */
    final public function getContainer(): ?AbstractRevisionContainer
    {
        static::checkRevisionContainer($this->container);

        return $this->container;
    }

    /**
     * Set container of this revision.
     *
     * @param AbstractRevisionContainer|null $container
     */
    final public function setContainer(?AbstractRevisionContainer $container): void
    {
        static::checkRevisionContainer($container);
        if ($this->container && $this->container !== $container) {
            $this->container->removeRevision($this);
        }
        if ($container && $container !== $this->container) {
            $this->container = $container;
            $container->addRevision($this);
        }
    }

    /**
     * Check if this revision is actual/active in specified datetime (or now if datetime is not specified).
     *
     * @param DateTime|null $dateTime
     *
     * @return bool
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
