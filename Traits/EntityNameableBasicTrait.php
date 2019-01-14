<?php

namespace Zakjakub\OswisResourcesBundle\Traits;


use Zakjakub\OswisResourcesBundle\Entity\Type\Nameable;

trait EntityNameableBasicTrait
{
    use EntityIdTrait;
    use EntityNameTrait;
    use EntityDescriptionTrait;
    use EntitySingleNoteTrait;
    use EntityTimestampableTrait;

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
