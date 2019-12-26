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
     */
    public ?string $name = null;

    /**
     * Shortened name.
     */
    public ?string $shortName = null;

    /**
     * Description.
     */
    public ?string $description = null;

    /**
     * Note.
     */
    public ?string $note = null;

    /**
     * Internal note.
     */
    public ?string $internalNote = null;

    /**
     * Slug (string for ie. url).
     */
    public ?string $slug = null;

    /**
     * Constructor of nameable.
     */
    public function __construct(
        ?string $name = null,
        ?string $shortName = null,
        ?string $description = null,
        ?string $note = null,
        ?string $slug = null,
        ?string $internalNote = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->note = $note;
        $this->slug = $slug;
        $this->internalNote = $internalNote;
    }
}
