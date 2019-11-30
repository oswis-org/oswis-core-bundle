<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity;

/**
 * Definition for column in PDF export file.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class PdfListColumn
{
    public const TYPE_BASIC = '';
    public const TYPE_BOOLEAN = 'bool';
    public const TYPE_DATE = 'date';
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_URL = 'url';
    public const TYPE_EMAIL = 'email';
    public const TYPE_ID = 'id';
    public const TYPE_ID_DATE = 'id+date';
    public const TYPE_ID_DATETIME = 'id+datetime';

    /**
     * @var string|null
     *
     * @example dateTime
     */
    public ?string $name = null;

    /**
     * @var string|null
     *
     * @example Datum a čas
     */
    public ?string $title = null;

    /**
     * @var string|null
     *
     * @example #006FAD
     * @example rgba(0,0,0,1)
     */
    public ?string $color = null;

    /**
     * @var string|null
     *
     * @example #006FAD
     * @example rgba(0,0,0,1)
     */
    public ?string $backgroundColor = null;

    /**
     * @var string|null
     *
     * @example bold
     */
    public ?string $fontWeight = null;

    /**
     * @var string|null
     *
     * @example center
     */
    public ?string $textAlign = null;

    /**
     * @var string|null
     *
     * @example middle
     */
    public ?string $verticalAlign = null;

    /**
     * @var string|null
     *
     * @example datetime
     * @example id+datetime
     */
    public ?string $type = null;

    /**
     * PdfListColumn constructor.
     *
     * @param string $name
     * @param string $title
     * @param string $color
     * @param string $backgroundColor
     * @param string $fontWeight
     * @param string $textAlign
     * @param string $verticalAlign
     * @param string $type
     */
    public function __construct(
        ?string $name = null,
        ?string $type = null,
        ?string $title = null,
        ?string $textAlign = null,
        ?string $fontWeight = null,
        ?string $color = null,
        ?string $backgroundColor = null,
        ?string $verticalAlign = null
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->color = $color;
        $this->backgroundColor = $backgroundColor;
        $this->fontWeight = $fontWeight;
        $this->textAlign = $textAlign;
        $this->verticalAlign = $verticalAlign;
        $this->type = $type;
    }
}
