<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\EMail;

use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\PriorityInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\TypeInterface;

interface EMailCategoryInterface extends NameableInterface, TypeInterface, PriorityInterface
{
}