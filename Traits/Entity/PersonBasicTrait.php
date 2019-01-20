<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

trait PersonBasicTrait
{
    use BasicEntityTrait;
    use FullNameTrait;
    use BirthDateTrait;
    use SingleNoteTrait;
    use DescriptionTrait;
}
