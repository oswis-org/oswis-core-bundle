<?php /** @noinspection PhpUnused */

namespace OswisOrg\OswisCoreBundle\Utils;

use Exception;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function chr;
use function ord;

class StringUtils
{
    private const ACCENTS_TABLE = [
        'ä' => 'a',
        'Ä' => 'A',
        'á' => 'a',
        'Á' => 'A',
        'à' => 'a',
        'À' => 'A',
        'ã' => 'a',
        'Ã' => 'A',
        'â' => 'a',
        'Â' => 'A',
        'č' => 'c',
        'Č' => 'C',
        'ć' => 'c',
        'Ć' => 'C',
        'ď' => 'd',
        'Ď' => 'D',
        'ě' => 'e',
        'Ě' => 'E',
        'é' => 'e',
        'É' => 'E',
        'ë' => 'e',
        'Ë' => 'E',
        'è' => 'e',
        'È' => 'E',
        'ê' => 'e',
        'Ê' => 'E',
        'í' => 'i',
        'Í' => 'I',
        'ï' => 'i',
        'Ï' => 'I',
        'ì' => 'i',
        'Ì' => 'I',
        'î' => 'i',
        'Î' => 'I',
        'ľ' => 'l',
        'Ľ' => 'L',
        'ĺ' => 'l',
        'Ĺ' => 'L',
        'ń' => 'n',
        'Ń' => 'N',
        'ň' => 'n',
        'Ň' => 'N',
        'ñ' => 'n',
        'Ñ' => 'N',
        'ó' => 'o',
        'Ó' => 'O',
        'ö' => 'o',
        'Ö' => 'O',
        'ô' => 'o',
        'Ô' => 'O',
        'ò' => 'o',
        'Ò' => 'O',
        'õ' => 'o',
        'Õ' => 'O',
        'ő' => 'o',
        'Ő' => 'O',
        'ř' => 'r',
        'Ř' => 'R',
        'ŕ' => 'r',
        'Ŕ' => 'R',
        'š' => 's',
        'Š' => 'S',
        'ś' => 's',
        'Ś' => 'S',
        'ť' => 't',
        'Ť' => 'T',
        'ú' => 'u',
        'Ú' => 'U',
        'ů' => 'u',
        'Ů' => 'U',
        'ü' => 'u',
        'Ü' => 'U',
        'ù' => 'u',
        'Ù' => 'U',
        'ũ' => 'u',
        'Ũ' => 'U',
        'û' => 'u',
        'Û' => 'U',
        'ý' => 'y',
        'Ý' => 'Y',
        'ž' => 'z',
        'Ž' => 'Z',
        'ź' => 'z',
        'Ź' => 'Z',
    ];

    public static function startsWith(string $haystack, string $needle): bool
    {
        return 0 === mb_strpos($haystack, $needle);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        return (mb_strlen($haystack) >= mb_strlen($needle)) && (false !== mb_strpos($haystack, $needle, mb_strlen($haystack) - mb_strlen($needle)));
    }

    public static function capitalize(string $text): string
    {
        return mb_strtoupper(mb_substr($text, 0, 1)).mb_substr($text, 1, mb_strlen($text));
    }

    public static function shorten(string $text, int $length): string
    {
        if (mb_strlen($text) - 3 > $length) {
            $text = mb_substr($text, 0, $length - 3).'...';
        }

        return $text;
    }

    public static function hyphenize(?string $text): ?string
    {
        return empty($text)
            ? null
            : (new AsciiSlugger())->slug($text)
                ->lower()
                ->toString();
    }

    public static function removeAccents(string $text): string
    {
        return strtr($text, self::ACCENTS_TABLE);
    }

    public static function hyphensToCamel(string $text, bool $uncapitalize = true): string
    {
        return self::convertToCamel($text, '-', $uncapitalize);
    }

    public static function uncapitalize(string $text): string
    {
        return mb_strtolower(mb_substr($text, 0, 1)).mb_substr($text, 1, mb_strlen($text));
    }

    public static function snakeToCamel(string $text, bool $uncapitalize = true): string
    {
        return self::convertToCamel($text, '_', $uncapitalize);
    }

    public static function camelToHyphens(string $text): string
    {
        return self::convertFromCamel($text, '-');
    }

    public static function camelToSnake(string $text): string
    {
        return self::convertFromCamel($text, '_');
    }

    public static function generatePassword(bool $addSpecialChar = false): string
    {
        $numbers = self::randomString('0', '9', 3);
        $lowerCase = self::randomString('a', 'z', 3);
        $upperCase = self::randomString('A', 'Z', 3);
        $specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '.', ','];
        $password = $numbers.$lowerCase.$upperCase;
        if ($addSpecialChar) {
            $password .= $specialChars[array_rand($specialChars)];
        }

        return str_shuffle($password);
    }

    /**
     * @throws Exception
     */
    public static function generateToken(): string
    {
        $numbers = self::randomString('0', '9', 4);
        $lowerCase = self::randomString('a', 'z', 4);
        $upperCase = self::randomString('A', 'Z', 4);
        $password = $numbers.$lowerCase.$upperCase;

        return str_shuffle($password);
    }

    private static function convertToCamel(string $text, string $separator, bool $uncapitalize = true): string
    {
        $result = str_replace(' ', '', mb_convert_case(str_replace($separator, ' ', $text), MB_CASE_TITLE));

        return $uncapitalize ? self::uncapitalize($result) : $result;
    }

    private static function convertFromCamel(string $text, string $separator): string
    {
        return ltrim(mb_strtolower(preg_replace('/[A-Z]/', $separator.'$0', $text)), $separator);
    }

    /**
     * @param string $from   Start char
     * @param string $to     End char
     * @param int    $length Length
     *
     * @return string Random string from required chars
     */
    private static function randomString(string $from, string $to, int $length): ?string
    {
        $str = '';
        if ($length > 1) {
            $str = self::randomString($from, $to, --$length);
        }
        try {
            return $str.chr(random_int(ord($from), ord($to)));
        } catch (Exception $e) {
            return null;
        }
    }
}
