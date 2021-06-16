<?php
/**
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service\Web;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\SiteMapItem;
use OswisOrg\OswisCoreBundle\Interfaces\Web\SiteMapExtenderInterface;

class SiteMapService
{
    protected Collection $extenders;

    public function __construct()
    {
        $this->extenders = new ArrayCollection();
    }

    public function addExtender(SiteMapExtenderInterface $extender): void
    {
        $this->extenders->add($extender);
    }

    /**
     * @return Collection<SiteMapItem>
     */
    public function getItems(): Collection
    {
        $items = new ArrayCollection();
        foreach ($this->extenders as $extender) {
            if ($extender instanceof SiteMapExtenderInterface) {
                foreach ($extender->getItems() as $item) {
                    $items->add($item);
                }
            }
        }

        return $items;
    }

}
