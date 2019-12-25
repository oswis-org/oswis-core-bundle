<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Nameable;

trait NameableBasicTrait
{
    use NameTrait;
    use DescriptionTrait;
    use NoteTrait;
    use SlugTrait;
    use InternalNoteTrait; /// ????? Is used somewhere?

    public function setFieldsFromNameable(?Nameable $nameable = null): void
    {
        if ($nameable) {
            $this->setName($nameable->name);
            $this->setDescription($nameable->description);
            $this->setShortName($nameable->shortName);
            $this->setNote($nameable->note);
            $this->setSlug($nameable->slug);
        }
    }

    public function getNameable(): Nameable
    {
        return new Nameable($this->getName(), $this->getShortName(), $this->getDescription(), $this->getNote(), $this->getSlug());
    }
}
