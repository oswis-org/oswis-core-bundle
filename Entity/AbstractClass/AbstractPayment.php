<?php

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Interfaces\BasicEntityInterface;
use OswisOrg\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\BasicMailConfirmationTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\DateTimeTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\ExternalIdTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\InternalNoteTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\NoteTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\NumericValueTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\TypeTrait;

/**
 * Abstract class containing basic properties for payment.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractPayment implements BasicEntityInterface
{
    use BasicEntityTrait;
    use DateTimeTrait;
    use NumericValueTrait;
    use TypeTrait;
    use NoteTrait;
    use InternalNoteTrait;
    use BasicMailConfirmationTrait;
    use ExternalIdTrait;

    public static function getAllowedTypesDefault(): array
    {
        return ['', 'administration', 'manual-db', 'csv'];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }
}
