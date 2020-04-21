<?php /** @noinspection PhpUnused */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Interfaces\BasicEntityInterface;
use OswisOrg\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\TextValueTrait;
use OswisOrg\OswisCoreBundle\Traits\Entity\TypeTrait;

/**
 * Abstract class containing basic properties for web pages contents.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractWebContent implements BasicEntityInterface
{
    public const HTML = 'html';
    public const CSS = 'css';
    public const JS = 'js';

    use BasicEntityTrait;
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
