<?php

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Interfaces\PersonInterface;
use OswisOrg\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\ForeignNationalityTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\IdCardTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\PersonBasicTrait;

/**
 * Abstract class containing basic properties for person.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractPerson implements PersonInterface
{
    use BasicEntityTrait;
    use PersonBasicTrait;
    use ForeignNationalityTrait;
    use IdCardTrait;
}
