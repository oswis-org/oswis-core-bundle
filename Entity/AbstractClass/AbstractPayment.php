<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisNotImplementedException;
use OswisOrg\OswisCoreBundle\Interfaces\Payment\PaymentInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicMailConfirmationTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;
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
    use BasicTrait;
    use NumericValueTrait;
    use TypeTrait;
    use NoteTrait;
    use InternalNoteTrait;
    use BasicMailConfirmationTrait;
    use ExternalIdTrait;
    use DateTimeTrait {
        getDateTime as protected traitGetDateTime;
    }

    /**
     * AbstractPayment constructor.
     *
     * @param int|null      $numericValue
     * @param string|null   $type
     * @param string|null   $note
     * @param string|null   $internalNote
     * @param string|null   $externalId
     * @param DateTime|null $dateTime
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ?int $numericValue = null,
        ?string $type = null,
        ?string $note = null,
        ?string $internalNote = null,
        ?string $externalId = null,
        ?DateTime $dateTime = null
    ) {
        $this->setNumericValue($numericValue);
        $this->setType($type);
        $this->setNote($note);
        $this->setInternalNote($internalNote);
        $this->setExternalId($externalId);
        $this->dateTime = $dateTime ?? new DateTime();
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
     * @param DateTime|null $dateTime
     *
     * @throws OswisNotImplementedException
     */
    public function setDateTime(?DateTime $dateTime): void
    {
        if ($this->getDateTime() !== $dateTime) {
            throw new OswisNotImplementedException('změna data platby');
        }
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
