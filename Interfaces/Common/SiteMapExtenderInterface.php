<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use Doctrine\Common\Collections\Collection;

interface SiteMapExtenderInterface
{
    public function getItems(): Collection;
}
