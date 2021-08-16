<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Mail;

use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\PriorityInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\TypeInterface;

interface MailCategoryInterface extends NameableInterface, TypeInterface, PriorityInterface
{
}