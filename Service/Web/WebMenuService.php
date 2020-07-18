<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service\Web;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\WebMenuItem;
use OswisOrg\OswisCoreBundle\Interfaces\Web\SiteMapExtenderInterface;
use OswisOrg\OswisCoreBundle\Interfaces\WebAdmin\WebAdminMenuExtenderInterface;

class WebMenuService
{
    protected ?Collection $extenders;

    public function __construct()
    {
        $this->extenders = new ArrayCollection();
    }

    public function addExtender(SiteMapExtenderInterface $extender): void
    {
        $this->extenders->add($extender);
    }

    /**
     * @return Collection<WebMenuItem>
     */
    public function getItems(?string $menu = null): Collection
    {
        $items = new ArrayCollection();
        foreach ($this->extenders as $extender) {
            if ($extender instanceof WebAdminMenuExtenderInterface) {
                foreach ($extender->getItems() as $item) {
                    if ($item instanceof WebMenuItem) {
                        /** @noinspection IsEmptyFunctionUsageInspection */
                        if (empty($item) || $item->hasMenu($menu)) {
                            $items->add($item);
                        }
                    }
                }
            }
        }

        return $items;
    }
}
