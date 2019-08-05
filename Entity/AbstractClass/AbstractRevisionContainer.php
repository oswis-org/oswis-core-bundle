<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionContainerInterface;

abstract class AbstractRevisionContainer implements RevisionContainerInterface
{

    /**
     * @var Collection
     */
    protected $revisions;

    /**
     * @var AbstractRevision|null
     */
    protected $activeRevision;

    /**
     * @return AbstractRevision|null
     */
    final public function getActiveRevision(): ?AbstractRevision
    {
        $this->updateActiveRevision();

        return $this->activeRevision;
    }

    /**
     * @param AbstractRevision $activeRevision
     */
    final public function setActiveRevision(AbstractRevision $activeRevision): void
    {
        $this->activeRevision = $activeRevision;
        $this->updateActiveRevision();
    }

    final public function updateActiveRevision(): void
    {
        try {
            $lastRevision = $this->getRevision();
            if ($lastRevision !== $this->activeRevision) {
                $this->activeRevision = $lastRevision;
            }
        } catch (RevisionMissingException $e) {
            return;
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return AbstractRevision
     * @throws RevisionMissingException
     */
    final public function getRevision(DateTime $dateTime = null): AbstractRevision
    {
        if (!$dateTime) {
            return $this->activeRevision;
        }

        $revisions = $this->getRevisionsOlderThanDateTime($dateTime);
        if (!$revisions || !$revisions[0]) {
            throw new RevisionMissingException((static::getRevisionClassName() ?? 'Revision class').' not found.');
        }

        static::checkRevision($revisions[0]);

        return $revisions[0];
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return array
     */
    final public function getRevisionsOlderThanDateTime(DateTime $dateTime = null): array
    {
        try {
            $dateTime = $dateTime ?? new DateTime();
        } catch (Exception $e) {
        }
        $revisions = $this->getRevisions()->filter(
            static function (AbstractRevision $revision) use ($dateTime) {
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
        $this->updateActiveRevision();
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
        $this->updateActiveRevision();
    }

    /**
     * @param DateTime|null $referenceDateTime
     *
     * @return DateTime|null
     * @throws RevisionMissingException
     */
    final public function getLastRevisionDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevision($referenceDateTime)->getCreatedDateTime();
    }

}
