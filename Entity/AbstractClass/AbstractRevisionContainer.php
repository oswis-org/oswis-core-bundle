<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionContainerInterface;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionInterface;

/**
 * Abstract class representing container of revisions/versions of some entity (of some entity which extends AbstractRevision).
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractRevisionContainer implements RevisionContainerInterface
{
    /**
     * Revisions/versions of this container.
     *
     * @var Collection<AbstractRevision>|null
     */
    protected ?Collection $revisions = null;

    /**
     * Revision/version which is actual/active now.
     *
     * @var AbstractRevision|null
     */
    protected ?AbstractRevision $activeRevision = null;

    /**
     * Check validity of some revision/version (ie. for use before adding revision).
     */
    abstract public static function checkRevision(?AbstractRevision $revision): void;

    /**
     * Class name of revisions/versions stored in this container.
     */
    abstract public static function getRevisionClassName(): string;

    /**
     * Add some revision/version to this container.
     */
    final public function addRevision(?AbstractRevision $revision): void
    {
        if (!$revision) {
            return;
        }
        if (!$this->revisions) {
            $this->revisions = new ArrayCollection();
        }
        static::checkRevision($revision);
        if (!$this->revisions->contains($revision)) {
            $this->revisions->add($revision);
            $revision->setContainer($this);
        }
        $this->setActiveRevision($revision);
    }

    /**
     * Remove some revision/version from this container.
     */
    final public function removeRevision(?AbstractRevision $revision): void
    {
        if (!$revision) {
            return;
        }
        if (!$this->revisions) {
            $this->revisions = new ArrayCollection();
        }
        static::checkRevision($revision);
        if ($this->revisions->removeElement($revision)) {
            $revision->setContainer(null);
        }
        if ($revision === $this->getActiveRevision()) {
            $this->updateActiveRevision();
        }
    }

    /**
     * Revision/version which is actual/active now.
     */
    final public function getActiveRevision(): ?AbstractRevision
    {
        if (!$this->activeRevision) {
            $this->updateActiveRevision();
        }

        return $this->activeRevision;
    }

    /**
     * Set revision/version which is actual/active now.
     *
     * @param AbstractRevision $activeRevision
     */
    final public function setActiveRevision(?AbstractRevision $activeRevision): void
    {
        $this->activeRevision = $activeRevision;
    }

    /**
     * Automatically set revision/version which is actual/active now.
     * @noinspection MethodShouldBeFinalInspection
     */
    public function updateActiveRevision(): void
    {
        try {
            $lastRevision = $this->getRevision(null, true);
            if ($lastRevision !== $this->activeRevision) {
                $this->activeRevision = $lastRevision;
            }
        } catch (RevisionMissingException $e) {
            return;
        }
    }

    /**
     * Get revision/version which is (was) active in specified date and time (or now if dateTime is not specified).
     *
     * @throws RevisionMissingException
     */
    final public function getRevision(DateTime $dateTime = null, ?bool $force = false): AbstractRevision
    {
        if (!$force && !$dateTime && $this->activeRevision) {
            return $this->activeRevision;
        }
        $revisions = $this->getRevisionsOlderThanDateTime($dateTime);
        if (empty($revisions) || empty($revisions[0])) {
            throw new RevisionMissingException((static::getRevisionClassName() ?? 'Revision class').' not found.');
        }
        static::checkRevision($revisions[0]);
        if (!$dateTime && (!$this->activeRevision || $this->activeRevision !== $revisions[0])) {
            $this->activeRevision = $revisions[0];
        }

        return $revisions[0];
    }

    final public function getRevisionsOlderThanDateTime(DateTime $dateTime = null): array
    {
        try {
            $dateTime ??= new DateTime() ?? null;
        } catch (Exception $e) {
            $dateTime ??= null;
        }
        $revisions = $this->getRevisions()->filter(fn(AbstractRevision $revision) => $dateTime >= $revision->getCreatedDateTime())->toArray();
        AbstractRevision::sortByCreatedDateTime($revisions);

        return $revisions;
    }

    /**
     * All revisions/versions of this container.
     * @return Collection<RevisionInterface>
     */
    final public function getRevisions(): Collection
    {
        return $this->revisions ?? new ArrayCollection();
    }

    /**
     * Get date and time of active/actual revision/version in some date and time (or now if referenceDateTime is not specified).
     * @throws RevisionMissingException
     */
    final public function getLastRevisionDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevision($referenceDateTime)->getCreatedDateTime();
    }
}
