<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Extender;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\SiteMapItem;
use OswisOrg\OswisCoreBundle\Interfaces\Common\SiteMapExtenderInterface;

class OswisCoreSitemapExtender implements SiteMapExtenderInterface
{
    public function getItems(): Collection
    {
        return new ArrayCollection([new SiteMapItem('SOME_FOO_BAR_ADDRESS_OSWIS_CORE', new DateTime(), 1, 'daily')]);
    }
}