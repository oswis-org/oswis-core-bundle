<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\User;

use OswisOrg\OswisCoreBundle\Traits\AddressBook\EmailTrait;
use OswisOrg\OswisCoreBundle\Traits\AddressBook\PersonTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\DateRangeTrait;

trait UserTrait
{
    use PersonTrait;
    use EmailTrait;
    use UsernameTrait;
    use EncryptedPasswordTrait;
    use PasswordResetTrait;
    use AccountActivationTrait;
    use DateRangeTrait;

    public function isActivated(): bool
    {
        return $this->isAccountActivated();
    }
}
