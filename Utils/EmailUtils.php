<?php

namespace Zakjakub\OswisCoreBundle\Utils;

class EmailUtils
{
    public static function mimeEnc(string $value): string
    {
        return self::mime_header_encode($value);
    }

    public static function mime_header_encode(string $text, string $encoding = 'utf-8'): string
    {
        return '=?'.$encoding.'?B?'.base64_encode($text).'?=';
    }
}
