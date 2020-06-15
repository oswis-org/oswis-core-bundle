<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\EMail;

use OswisOrg\OswisCoreBundle\Interfaces\Common\DateRangeInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\PriorityInterface;

interface EMailGroupInterface extends NameableInterface, PriorityInterface, DateRangeInterface
{
}