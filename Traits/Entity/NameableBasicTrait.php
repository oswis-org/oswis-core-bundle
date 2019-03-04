<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Nameable;

trait NameableBasicTrait
{
    use NameTrait;
    use DescriptionTrait;
    use NoteTrait;

    final public function setFieldsFromNameable(?Nameable $nameable = null): void
    {
        if ($nameable) {
            $this->setName($nameable->name);
            $this->setDescription($nameable->description);
            $this->setShortName($nameable->shortName);
            $this->setNote($nameable->note);
        }
    }
}
