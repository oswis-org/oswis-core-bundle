<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity;

/**
 * Definition for column in PDF export file.
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
     * @var string
     * @example dateTime
     */
    public $name;

    /**
     * @var string
     * @example Datum a čas
     */
    public $title;

    /**
     * @var string
     * @example #006FAD
     * @example rgba(0,0,0,1)
     */
    public $color;

    /**
     * @var string
     * @example #006FAD
     * @example rgba(0,0,0,1)
     */
    public $backgroundColor;

    /**
     * @var string
     * @example bold
     */
    public $fontWeight;

    /**
     * @var string
     * @example center
     */
    public $textAlign;

    /**
     * @var string
     * @example middle
     */
    public $verticalAlign;

    /**
     * @var string
     * @example id+datetime
     */
    public $type;

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
