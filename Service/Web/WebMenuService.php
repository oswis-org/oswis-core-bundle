<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service\Web;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\WebMenuItem;
use OswisOrg\OswisCoreBundle\Interfaces\Web\WebMenuExtenderInterface;

class WebMenuService
{
    protected ?Collection $extenders;

    public function __construct()
    {
        $this->extenders = new ArrayCollection();
    }

    public function addExtender(WebMenuExtenderInterface $extender): void
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
            if ($extender instanceof WebMenuExtenderInterface) {
                foreach ($extender->getItems() as $item) {
                    /** @noinspection IsEmptyFunctionUsageInspection */
                    if ($item instanceof WebMenuItem && (empty($item) || $item->hasMenu($menu))) {
                        $items->add($item);
                    }
                }
            }
        }

        return $items;
    }
}
