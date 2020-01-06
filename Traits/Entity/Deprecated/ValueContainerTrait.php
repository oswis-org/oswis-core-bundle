<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait ValueContainerTrait
{
    public function setValueRegex(string $valueRegex): void
    {
        if ($this->getValueRegex() !== $valueRegex) {
            $newRevision = clone $this->getRevision();
            $newRevision->setValueRegex($valueRegex);
            $this->addRevision($newRevision);
        }
    }

    public function getValueRegex(?DateTime $referenceDateTime = null): string
    {
        return $this->getRevisionByDate($referenceDateTime)->getValueRegex();
    }

    /**
     * @param string $valueLabel
     */
    public function setValueLabel(?string $valueLabel): void
    {
        if ($this->getValueLabel() !== $valueLabel) {
            $newRevision = clone $this->getRevision();
            $newRevision->setValueLabel($valueLabel);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @return string
     */
    public function getValueLabel(?DateTime $referenceDateTime = null): ?string
    {
        return $this->getRevisionByDate($referenceDateTime)->getValueLabel();
    }

    public function isValueAllowed(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->getValueAllowed();
    }

    /**
     * @param bool $valueAllowed
     */
    public function setValueAllowed(?bool $valueAllowed): void
    {
        if ($this->getValueAllowed() !== $valueAllowed) {
            $newRevision = clone $this->getRevision();
            $newRevision->setValueAllowed($valueAllowed);
            $this->addRevision($newRevision);
        }
    }
}
