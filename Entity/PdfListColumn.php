<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use function in_array;

/**
 * Class PdfListColumn
 * @package Zakjakub\OswisCoreBundle\Entity
 */
class PdfListColumn
{
    public const TYPE_BASIC = '';

    public const ALLOWED_TYPES = [self::TYPE_BASIC];

    /**
     * @var string
     * @example
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $color;

    /**
     * @var string
     */
    public $backgroundColor;

    /**
     * @var string
     */
    public $fontWeight;

    /**
     * @var string
     */
    public $textAlign;

    /**
     * @var string
     */
    public $verticalAlign;

    /**
     * @var string
     */
    protected $type;

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
        string $name = null,
        string $type = null,
        string $title = null,
        string $textAlign = null,
        string $fontWeight = null,
        string $color = null,
        string $backgroundColor = null,
        string $verticalAlign = null
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

    final public function setType(?string $newType): void
    {
        if (in_array($newType, self::ALLOWED_TYPES, true)) {
            $this->type = $newType;
        }
    }
}
