<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Zakjakub\OswisCoreBundle\Entity\Nameable;

trait NameableBasicTrait
{
    use IdTrait;
    use NameTrait;
    use DescriptionTrait;
    use SingleNoteTrait;
    use TimestampableTrait;

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
