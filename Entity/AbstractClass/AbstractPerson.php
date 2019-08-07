<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Zakjakub\OswisCoreBundle\Interfaces\PersonInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\ForeignNationalityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\IdCardTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\PersonBasicTrait;

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
