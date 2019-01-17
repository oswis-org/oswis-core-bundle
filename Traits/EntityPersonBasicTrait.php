<?php

namespace Zakjakub\OswisCoreBundle\Traits;

trait EntityPersonBasicTrait
{
    use EntityIdTrait;
    use EntityFullNameTrait;
    use EntityBirthDateTrait;
    use EntityTimestampableTrait;
    use EntitySingleNoteTrait;
    use EntityDescriptionTrait;
}
