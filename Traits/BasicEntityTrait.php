<?php

namespace Zakjakub\OswisCoreBundle\Traits;

/**
 * Trait adds basic fields (id, createdDateTime, updatedDateTime...).
 */
trait BasicEntityTrait
{

    use EntityIdTrait;
    use EntityTimestampableTrait;
    use BlameableTrait;

}
