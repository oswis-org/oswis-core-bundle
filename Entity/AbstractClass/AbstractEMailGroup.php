<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\PriorityTrait;

/**
 * EMail group contains restrictions of recipients.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractEMailGroup
{
    use NameableTrait;
    use PriorityTrait;

    public function __construct(?Nameable $nameable = null, ?int $priority = null)
    {
        $this->setFieldsFromNameable($nameable);
        $this->setPriority($priority);
    }
}
