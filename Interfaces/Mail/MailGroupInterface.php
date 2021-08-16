<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Mail;

use OswisOrg\OswisCoreBundle\Interfaces\Common\DateRangeInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\PriorityInterface;

interface MailGroupInterface extends NameableInterface, PriorityInterface, DateRangeInterface
{
}