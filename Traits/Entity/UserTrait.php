<?php

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

trait UserTrait
{
    use PersonBasicTrait;
    use EmailTrait;
    use PhoneTrait;
    use UrlTrait;
    use UsernameTrait;
    use EncryptedPasswordTrait;
    use PasswordResetTrait;
    use AccountActivationTrait;
    use DateRangeTrait;
    use PasswordSaltTrait;
}
