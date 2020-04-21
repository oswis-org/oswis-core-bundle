<?php

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Interfaces\BasicEntityInterface;
use OswisOrg\OswisCoreBundle\Traits\Entity\AddressTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\IdentificationNumberTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

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
