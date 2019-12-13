<?php /** @noinspection PhpUnused */
/** @noinspection UnknownInspectionInspection */
/** @noinspection RequiredAttributes */
/** @noinspection HtmlRequiredAltAttribute */
/** @noinspection HtmlUnknownTag */

namespace Zakjakub\OswisCoreBundle\Utils;

/**
 * Utilities for source codes.
 *
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class SourceCodeUtils
{
    public static function escapeHtml(string $input): string
    {
        return $input ? htmlspecialchars($input, ENT_QUOTES) : '';
    }

    /**
     * Strips HTML tags from string.
     */
    public static function stripHtml(
        string $input,
        string $allowedTags = '<ul><li><img><cite><h1><h2><h3><h4><h5><br><a><p>'
    ): string {
        return $input ? strip_tags($input, $allowedTags) : '';
    }
}
