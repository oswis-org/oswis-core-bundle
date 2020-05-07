<?php

namespace OswisOrg\OswisCoreBundle\Traits\User;

use OswisOrg\OswisCoreBundle\Traits\AddressBook\EmailTrait;
use OswisOrg\OswisCoreBundle\Traits\AddressBook\PersonBasicTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\DateRangeTrait;

trait UserTrait
{
    use PersonBasicTrait;
    use EmailTrait;
    use UsernameTrait;
    use EncryptedPasswordTrait;
    use PasswordResetTrait;
    use AccountActivationTrait;
    use DateRangeTrait;
    use PasswordSaltTrait;
}
