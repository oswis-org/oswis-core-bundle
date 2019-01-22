<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds basic fields (id, createdDateTime, updatedDateTime...).
 */
trait BasicEntityTrait
{
    use IdTrait;
    use TimestampableTrait;
    use BlameableTrait;
    use IpTraceableTrait;
}
