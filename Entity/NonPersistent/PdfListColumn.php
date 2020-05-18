<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

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
    public const TYPE_ID_USERNAME = 'id+username';
    public const TYPE_NAME_EMAIL = 'name+email';

    public const TYPES = [
        self::TYPE_BASIC,
        self::TYPE_BOOLEAN,
        self::TYPE_DATE,
        self::TYPE_DATETIME,
        self::TYPE_URL,
        self::TYPE_EMAIL,
        self::TYPE_ID,
        self::TYPE_ID_DATE,
        self::TYPE_ID_DATETIME,
        self::TYPE_ID_USERNAME,
        self::TYPE_NAME_EMAIL,
    ];

    /**
     * @example dateTime
     */
    public ?string $name = null;

    /**
     * @example Datum a čas
     */
    public ?string $title = null;

    /**
     * @example #006FAD
     * @example rgba(0,0,0,1)
     */
    public ?string $color = null;

    /**
     * @example #006FAD
     * @example rgba(0,0,0,1)
     */
    public ?string $backgroundColor = null;

    /**
     * @example bold
     */
    public ?string $fontWeight = null;

    /**
     * @example center
     */
    public ?string $textAlign = null;

    /**
     * @example middle
     */
    public ?string $verticalAlign = null;

    /**
     * @example datetime
     * @example id+datetime
     */
    public ?string $type = null;

    public function __construct(?string $name = null, ?string $type = null, ?string $title = null, ?string $textAlign = null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->textAlign = $textAlign;
        $this->type = $type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function getFontWeight(): ?string
    {
        return $this->fontWeight;
    }

    public function setFontWeight(?string $fontWeight): void
    {
        $this->fontWeight = $fontWeight;
    }

    public function getTextAlign(): ?string
    {
        return $this->textAlign;
    }

    public function setTextAlign(?string $textAlign): void
    {
        $this->textAlign = $textAlign;
    }

    public function getVerticalAlign(): ?string
    {
        return $this->verticalAlign;
    }

    public function setVerticalAlign(?string $verticalAlign): void
    {
        $this->verticalAlign = $verticalAlign;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
