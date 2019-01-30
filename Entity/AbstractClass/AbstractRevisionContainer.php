<?php

namespace Zakjakub\OswisAccommodationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionContainerInterface;

/**
 * Class AbstractRevisionContainer
 * @package App\Entity\AbstractClass
 */
abstract class AbstractRevisionContainer implements RevisionContainerInterface
{

    /**
     * @var Collection
     */
    protected $revisions;

    /**
     * @param \DateTime|null $dateTime
     *
     * @return AbstractRevision
     * @throws RevisionMissingException
     */
    final public function getRevision(\DateTime $dateTime = null): AbstractRevision
    {
        $revisions = $this->getRevisionsOlderThanDateTime($dateTime);
        if (!$revisions || !$revisions[0]) {
            throw new RevisionMissingException((static::getRevisionClassName() ?? 'Revision class').' not found.');
        }

        static::checkRevision($revisions[0]);

        return $revisions[0];
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return array
     */
    final public function getRevisionsOlderThanDateTime(\DateTime $dateTime = null): array
    {
        try {
            $dateTime = $dateTime ?? new \DateTime();
        } catch (\Exception $e) {
        }
        $revisions = $this->getRevisions()->filter(
            function (AbstractRevision $revision) use ($dateTime) {
                return $dateTime >= $revision->getCreatedDateTime();
            }
        )->toArray();
        AbstractRevision::sortByCreatedDateTime($revisions);

        return $revisions;
    }

    /**
     * @return Collection
     */
    final public function getRevisions(): Collection
    {
        return $this->revisions ?? new ArrayCollection();
    }

    /**
     * @return string
     */
    abstract public static function getRevisionClassName(): string;

    /**
     * @param AbstractRevision|null $revision
     */
    abstract public static function checkRevision(?AbstractRevision $revision): void;

    /**
     * @param AbstractRevision|null $revision
     */
    final public function addRevision(?AbstractRevision $revision): void
    {
        static::checkRevision($revision);
        if (!$revision) {
            return;
        }
        if (!$this->revisions->contains($revision)) {
            $this->revisions->add($revision);
            $revision->setContainer($this);
        }
    }

    /**
     * @param AbstractRevision|null $revision
     */
    final public function removeRevision(?AbstractRevision $revision): void
    {
        static::checkRevision($revision);
        if (!$revision) {
            return;
        }
        if ($this->revisions->removeElement($revision)) {
            $revision->setContainer(null);
        }
    }

}
