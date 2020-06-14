<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TypeTrait;

/**
 * E-mail category represents some type of message (activation, activation request, password change, password change request...).
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractEMailCategory
{
    public const TYPE_ACTIVATION = AppUserService::ACTIVATION;
    public const TYPE_ACTIVATION_REQUEST = AppUserService::ACTIVATION_REQUEST;
    public const TYPE_PASSWORD_RESET = AppUserService::PASSWORD_CHANGE;
    public const TYPE_PASSWORD_RESET_REQUEST = AppUserService::PASSWORD_CHANGE_REQUEST;

    use NameableTrait;
    use TypeTrait;

    protected ?AbstractEMailTemplate $template = null;

    /**
     * @throws InvalidTypeException
     */
    public function __construct(?Nameable $nameable = null, ?string $type = null, AbstractEMailTemplate $template = null)
    {
        $this->setFieldsFromNameable($nameable);
        $this->setType($type);
        $this->setTemplate($template);
    }

    public static function getAllowedTypesDefault(): array
    {
        return ['', self::TYPE_ACTIVATION, self::TYPE_ACTIVATION_REQUEST, self::TYPE_PASSWORD_RESET, self::TYPE_PASSWORD_RESET_REQUEST];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }

    public function getTemplateString(): ?string
    {
        return $this->getTemplate() ? $this->getTemplate()->getTemplate() : null;
    }

    abstract public function getTemplate(): ?AbstractEMailTemplate;

    abstract public function setTemplate(?AbstractEMailTemplate $template): ?AbstractEMailTemplate;
}
