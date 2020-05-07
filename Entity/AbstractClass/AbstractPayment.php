<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use OswisOrg\OswisCoreBundle\Interfaces\Payment\PaymentInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicEntityTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicMailConfirmationTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\DateTimeTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\ExternalIdTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\InternalNoteTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NoteTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NumericValueTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TypeTrait;

/**
 * Abstract class containing basic properties for payment.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractPayment implements PaymentInterface
{
    use BasicEntityTrait;
    use NumericValueTrait;
    use TypeTrait;
    use NoteTrait;
    use InternalNoteTrait;
    use BasicMailConfirmationTrait;
    use ExternalIdTrait;
    use DateTimeTrait {
        getDateTime as protected traitGetDateTime;
    }

    public static function getAllowedTypesDefault(): array
    {
        return ['', 'administration', 'manual-db', 'csv'];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }

    /**
     * Date and time of payment.
     *
     * Date and time of creation is returned if it's not overwritten by dateTime property.
     * This method overrides method from trait.
     *
     * @return DateTime|null
     */
    public function getDateTime(): ?DateTime
    {
        return $this->traitGetDateTime() ?? $this->getCreatedDateTime();
    }

}
