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
 * * createdIp, updatedIp
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
trait BasicEntityTrait
{
    use IdTrait;
    use CustomIdTrait;
    use TimestampableTrait;
    use BlameableTrait;
}
