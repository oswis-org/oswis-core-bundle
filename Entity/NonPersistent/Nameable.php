<?php

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

/**
 * Entity consisting from common "name and description" properties.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class Nameable
{
    public ?string $name = null;

    public ?string $shortName = null;

    public ?string $description = null;

    public ?string $note = null;

    public ?string $internalNote = null;

    public ?string $forcedSlug = null;

    public function __construct(
        ?string $name = null,
        ?string $shortName = null,
        ?string $description = null,
        ?string $note = null,
        ?string $forcedSlug = null,
        ?string $internalNote = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->note = $note;
        $this->forcedSlug = $forcedSlug;
        $this->internalNote = $internalNote;
    }
}
