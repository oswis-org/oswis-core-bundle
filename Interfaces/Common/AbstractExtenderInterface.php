<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use Doctrine\Common\Collections\Collection;

interface AbstractExtenderInterface
{
    public function getItems(): Collection;
}
