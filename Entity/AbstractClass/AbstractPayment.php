<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicMailConfirmationTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\DateTimeTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\InternalNoteTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NoteTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NumericValueTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\TypeTrait;

/**
 * Abstract class containing basic properties for payment.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractPayment
{
    use BasicEntityTrait;
    use DateTimeTrait;
    use NumericValueTrait;
    use TypeTrait;
    use NoteTrait;
    use InternalNoteTrait;
    use BasicMailConfirmationTrait;

    public static function getAllowedTypesDefault(): array
    {
        return ['', 'administration', 'manual-db', 'csv'];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }
}
