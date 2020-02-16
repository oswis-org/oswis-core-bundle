<?php
/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Utils;

use function strlen;

/**
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class ColorUtils
{
    final public static function isOppositeWhite(string $color): bool
    {
        [$red, $green, $blue] = sscanf($color, (4 === strlen($color)) ? '#%1x%1x%1x' : '#%2x%2x%2x');

        return ($red * 0.299 + $green * 0.587 + $blue * 0.114) > 186;
    }
}
