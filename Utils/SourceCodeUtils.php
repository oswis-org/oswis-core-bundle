<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection RequiredAttributes */
/** @noinspection HtmlRequiredAltAttribute */
/** @noinspection HtmlUnknownTag */

namespace Zakjakub\OswisCoreBundle\Utils;

/**
 * Class AgeUtils
 * @package OswisCoreBundle
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class SourceCodeUtils
{

    public static function escapeHtml(string $input): string
    {
        if (!$input) {
            return '';
        }

        return htmlspecialchars($input, ENT_QUOTES);
    }

    /**
     * Strips HTML tags from string.
     *
     * @param string $input
     * @param string $allowedTags
     *
     * @return string
     */
    public static function stripHtml(
        string $input,
        string $allowedTags = '<ul><li><img><cite><h1><h2><h3><h4><h5><br><a><p>'
    ): string {
        if (!$input) {
            return '';
        }

        return strip_tags($input, $allowedTags);
    }

}
