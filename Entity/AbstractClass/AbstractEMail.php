<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Traits\AddressBook\EmailTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TypeTrait;

/**
 * Some e-mail sent to some user.
 *
 * Nameable is used in that way
 *  - **name** => **subject**
 *  - **customID** => **Message-ID**
 *  - **internalNote** => **status messages**
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractEMail
{
    public const TYPE_ACTIVATION = 'activation';
    public const TYPE_ACTIVATION_REQUEST = 'activation-request';
    public const TYPE_PASSWORD_RESET = 'password-reset';
    public const TYPE_PASSWORD_RESET_REQUEST = 'password-reset-request';

    use NameableTrait;
    use TypeTrait;
    use EmailTrait;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $sent = null;

    /**
     * @throws InvalidTypeException
     */
    public function __construct(?Nameable $nameable = null, ?string $eMail = null, ?string $type = null)
    {
        $this->setFieldsFromNameable($nameable);
        $this->setEmail($eMail);
        $this->setType($type);
    }

    public static function getAllowedTypesDefault(): array
    {
        return ['', self::TYPE_ACTIVATION, self::TYPE_ACTIVATION_REQUEST, self::TYPE_PASSWORD_RESET, self::TYPE_PASSWORD_RESET_REQUEST];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }

    public function isSent(): bool
    {
        return (bool)$this->getSent();
    }

    public function getSent(): ?DateTime
    {
        return $this->sent;
    }

    public function setSent(?DateTime $sent): void
    {
        $this->sent = $sent;
    }
}
