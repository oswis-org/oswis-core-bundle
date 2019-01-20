<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

/**
 * Trait adds basic fields (id, createdDateTime, updatedDateTime...).
 */
trait BasicEntityTrait
{

    use IdTrait;
    use TimestampableTrait;
    use BlameableTrait;

}
