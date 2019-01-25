<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use Zakjakub\OswisCoreBundle\Interfaces\PersonInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\IdCardTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NationalityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\PersonBasicTrait;

abstract class AbstractPerson implements PersonInterface
{
    use PersonBasicTrait;
    use NationalityTrait;
    use IdCardTrait;
}
