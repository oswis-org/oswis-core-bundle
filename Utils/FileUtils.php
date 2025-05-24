<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Utils;

use function strlen;

class FileUtils
{
    final public static function humanReadableFileUploadMaxSize(): string
    {
        return self::humanReadableBytes(self::fileUploadMaxSize());
    }

    final public static function humanReadableBytes(int $bytes, int $decimals = 2, string $system = 'binary'): string
    {
        $mod = ('binary' === $system) ? 1024 : 1000;
        $units = [
            'binary' => ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
            'metric' => ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        ];
        $factor = floor((strlen((string)$bytes) - 1) / 3);

        return sprintf("%.{$decimals}f %s", $bytes / ($mod ** $factor), $units[$system][$factor]);
    }

    final public static function fileUploadMaxSize(): int
    {
        static $max_size = -1;
        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = self::parseSize(''.ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }
            $upload_max = self::parseSize(''.ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return is_int($max_size) ? $max_size : -1;
    }

    final public static function parseSize(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^\d.]/', '', $size);        // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            /** @noinspection SpellCheckingInspection */
            return (int)round(((float)$size) * (1024 ** stripos('bkmgtpezy', $unit[0])));
        }

        return (int)round((float)$size);
    }
}
