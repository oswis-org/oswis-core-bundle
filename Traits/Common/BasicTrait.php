<?php
/**
 * @noinspection PhpUnusedAliasInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds **common basic fields** for entities (id, created/updatedDateTime...).
 *
 * Trait adds **common basic fields**:
 * * id
 * * createdDateTime, updatedDateTime
 * * createdAuthor, updatedAuthor
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
trait BasicTrait
{
    use IdTrait;
    use TimestampableTrait;
    use BlameableTrait;
}
