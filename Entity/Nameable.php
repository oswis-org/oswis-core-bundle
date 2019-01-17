<?php

namespace Zakjakub\OswisCoreBundle\Entity;


/**
 * Class Nameable
 * @package OswisCoreBundle
 */
class Nameable
{

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $shortName;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $note;

    /**
     * Nameable constructor.
     *
     * @param string|null $name
     * @param string|null $shortName
     * @param string|null $description
     * @param string|null $note
     */
    public function __construct(?string $name = null, ?string $shortName = null, ?string $description = null, ?string $note = null)
    {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->note = $note;
    }


}