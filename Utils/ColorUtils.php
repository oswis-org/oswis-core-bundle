<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Utils;

use function strlen;

/**
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class ColorUtils
{
    final public static function isOppositeWhite(string $color): string
    {
        if (4 === strlen($color)) {
            [$r, $g, $b] = sscanf($color, '#%1x%1x%1x');
        } else {
            [$r, $g, $b] = sscanf($color, '#%2x%2x%2x');
        }

        return ($r * 0.299 + $g * 0.587 + $b * 0.114) > 186;
    }
}
