<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Zakjakub\OswisCoreBundle\Entity\Nameable;

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
        return (new AsciiSlugger())->slug(($this->getName() ?? $this->getShortName()))->lower()->toString();
    }

}
