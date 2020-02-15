<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Zakjakub\OswisCoreBundle\Interfaces\BasicEntityInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\AddressTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\IdentificationNumberTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

/**
 * Abstract class containing properties for organization.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractOrganization implements BasicEntityInterface
{
    use BasicEntityTrait;
    use NameableBasicTrait;
    use IdentificationNumberTrait;
    use AddressTrait;
}
