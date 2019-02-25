<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Zakjakub\OswisCoreBundle\Traits\Entity\AddressTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\IdentificationNumberTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

abstract class AbstractOrganization
{
    use BasicEntityTrait;
    use NameableBasicTrait;
    use IdentificationNumberTrait;
    use AddressTrait;
}
