<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionInterface;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;
use function array_reverse;
use function usort;

abstract class AbstractRevision implements RevisionInterface
{

    // abstract public function hasSameValues(AbstractRevision $revision): bool;

    /**
     * @var AbstractRevisionContainer
     */
    protected $container;

    /**
     * @return string
     */
    abstract public static function getRevisionContainerClassName(): string;

    /**
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
     * @return DateTime|null
     */
    abstract public function getCreatedDateTime(): ?DateTime;

    public static function cmpId(?int $a, ?int $b): int
    {
        if ($a === $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    abstract public function getId(): ?int;

    /**
     * @return AbstractRevisionContainer|null
     */
    final public function getContainer(): ?AbstractRevisionContainer
    {
        static::checkRevisionContainer($this->container);

        return $this->container;
    }

    /**
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
     * @param AbstractRevisionContainer|null $revision
     */
    abstract public static function checkRevisionContainer(?AbstractRevisionContainer $revision): void;

    final public function isActive(?DateTime $dateTime = null): bool
    {
        try {
            return $this === $this->container->getRevision($dateTime);
        } catch (RevisionMissingException $e) {
            return false;
        }
    }
}
