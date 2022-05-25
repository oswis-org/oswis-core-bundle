<?php

namespace OswisOrg\OswisCoreBundle\Enum;

enum AppUserEditTypeEnum: string
{
    case Username = 'username';
    case EMail = 'email';
    case Password = 'password';
}

