<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use Doctrine\Common\Collections\Collection;

interface RssExtenderInterface
{
    public function getItems(): Collection;
}
