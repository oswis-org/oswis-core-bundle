<?php

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TextValueTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TypeTrait;

/**
 * Abstract class containing basic properties for web pdf contents.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractWebContent implements BasicInterface
{
    public const HTML = 'html';
    public const CSS = 'css';
    public const JS = 'js';

    use BasicTrait;
    use TypeTrait;
    use TextValueTrait;

    /**
     * @param string|null $textValue
     * @param string|null $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct(?string $textValue = null, ?string $type = null)
    {
        $this->setType($type);
        $this->setTextValue($textValue);
    }

    public static function getAllowedTypesDefault(): array
    {
        return [self::HTML, self::CSS, self::JS,];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }
}
