<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\User;

use OswisOrg\OswisCoreBundle\Traits\AddressBook\EmailTrait;
use OswisOrg\OswisCoreBundle\Traits\AddressBook\PersonTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\ActivatedTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\DeletedTrait;

trait UserTrait
{
    use PersonTrait;
    use EmailTrait;
    use UsernameTrait;
    use EncryptedPasswordTrait;
    use ActivatedTrait;
    use DeletedTrait;

    public function isActive(): bool
    {
        return $this->isActivated() && !$this->isDeleted();
    }
}
