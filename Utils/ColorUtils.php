<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Utils;

use function strlen;

/**
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class ColorUtils
{
    final public static function isOppositeWhite(?string $color = null): bool
    {
        if (empty($color)) {
            return false;
        }
        [$red, $green, $blue] = sscanf($color, (4 === strlen($color)) ? '#%1x%1x%1x' : '#%2x%2x%2x') ?? [];

        /** @phpstan-ignore-next-line */
        return ($red * 0.299 + $green * 0.587 + $blue * 0.114) <= 186;
    }
}
