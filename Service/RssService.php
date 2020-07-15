<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\RssItem;
use OswisOrg\OswisCoreBundle\Interfaces\Common\RssExtenderInterface;

class RssService
{
    protected ?Collection $extenders;

    public function __construct()
    {
        $this->extenders = new ArrayCollection();
    }

    public function addExtender(RssExtenderInterface $extender): void
    {
        $this->extenders->add($extender);
    }

    /**
     * @return Collection<RssItem>
     */
    public function getItems(): Collection
    {
        $items = new ArrayCollection();
        foreach ($this->extenders as $extender) {
            if ($extender instanceof RssExtenderInterface) {
                foreach ($extender->getItems() as $item) {
                    $items->add($item);
                }
            }
        }

        return $items;
    }

}
