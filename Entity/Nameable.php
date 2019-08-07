<?php

namespace Zakjakub\OswisCoreBundle\Entity;

/**
 * Entity consisting from common "name and description" properties.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class Nameable
{
    /**
     * Full name.
     * @var string|null
     */
    public $name;

    /**
     * Shortened name.
     * @var string|null
     */
    public $shortName;

    /**
     * Description.
     * @var string|null
     */
    public $description;

    /**
     * Note.
     * @var string|null
     */
    public $note;

    /**
     * Slug (string for ie. url).
     * @var string|null
     */
    public $slug;

    /**
     * Constructor of nameable.
     *
     * @param string|null $name
     * @param string|null $shortName
     * @param string|null $description
     * @param string|null $note
     * @param string|null $slug
     */
    public function __construct(
        ?string $name = null,
        ?string $shortName = null,
        ?string $description = null,
        ?string $note = null,
        ?string $slug = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->note = $note;
        $this->slug = $slug;
    }
}
