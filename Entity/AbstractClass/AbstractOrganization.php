<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use Zakjakub\OswisCoreBundle\Traits\Entity\AddressTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\IdentificationNumberTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

abstract class AbstractOrganization
{
    use NameableBasicTrait;
    use IdentificationNumberTrait;
    use AddressTrait;
}
