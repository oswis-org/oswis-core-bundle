<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait ValueContainerTrait
{

    /**
     * @param string $valueRegex
     */
    final public function setValueRegex(string $valueRegex): void
    {
        if ($this->getValueRegex() !== $valueRegex) {
            $newRevision = clone $this->getRevision();
            $newRevision->setValueRegex($valueRegex);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $referenceDateTime
     *
     * @return string
     */
    final public function getValueRegex(?DateTime $referenceDateTime = null): string
    {
        return $this->getRevisionByDate($referenceDateTime)->getValueRegex();
    }

    /**
     * @param string $valueLabel
     */
    final public function setValueLabel(?string $valueLabel): void
    {
        if ($this->getValueLabel() !== $valueLabel) {
            $newRevision = clone $this->getRevision();
            $newRevision->setValueLabel($valueLabel);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $referenceDateTime
     *
     * @return string
     */
    final public function getValueLabel(?DateTime $referenceDateTime = null): ?string
    {
        return $this->getRevisionByDate($referenceDateTime)->getValueLabel();
    }

    /**
     * @param DateTime|null $referenceDateTime
     *
     * @return bool
     */
    final public function isValueAllowed(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->getValueAllowed();
    }

    /**
     * @param bool $valueAllowed
     */
    final public function setValueAllowed(?bool $valueAllowed): void
    {
        if ($this->getValueAllowed() !== $valueAllowed) {
            $newRevision = clone $this->getRevision();
            $newRevision->setValueAllowed($valueAllowed);
            $this->addRevision($newRevision);
        }
    }
}
