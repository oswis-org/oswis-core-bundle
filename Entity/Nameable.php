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
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Shortened name.
     *
     * @var string|null
     */
    public ?string $shortName = null;

    /**
     * Description.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Note.
     *
     * @var string|null
     */
    public ?string $note = null;

    /**
     * Slug (string for ie. url).
     *
     * @var string|null
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
        ?string $slug = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->note = $note;
        $this->slug = $slug;
    }
}
