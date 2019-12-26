<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Nameable;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;

trait NameableBasicTrait
{
    use NameTrait;
    use DescriptionTrait;
    use NoteTrait;
    use SlugTrait;
    use InternalNoteTrait;

    public function setFieldsFromNameable(?Nameable $nameable = null): void
    {
        if ($nameable) {
            $this->setName($nameable->name);
            $this->setDescription($nameable->description);
            $this->setShortName($nameable->shortName);
            $this->setNote($nameable->note);
            $this->setSlug($nameable->slug);
            $this->setInternalNote($nameable->internalNote);
        }
    }

    public function getNameable(): Nameable
    {
        return new Nameable($this->getName(), $this->getShortName(), $this->getDescription(), $this->getNote(), $this->getSlug(), $this->getInternalNote());
    }

    public function updateSlug(): string
    {
        return $this->setSlug($this->getForcedSlug() ?? $this->getAutoSlug());
    }

    public function getAutoSlug(): ?string
    {
        return StringUtils::hyphenize($this->getName() ?? $this->getShortName());
    }

}
