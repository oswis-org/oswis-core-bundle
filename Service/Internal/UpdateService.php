<?php
/**
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service\Internal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Internal\UpdateItem;
use OswisOrg\OswisCoreBundle\Interfaces\Extender\UpdateExtenderInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Web\WebMenuExtenderInterface;

class UpdateService
{
    protected Collection $extenders;

    public function __construct()
    {
        $this->extenders = new ArrayCollection();
    }

    public function addExtender(WebMenuExtenderInterface $extender): void
    {
        $this->extenders->add($extender);
    }

    public function callItems(): void
    {
        foreach ($this->getItems() as $item) {
            if ($item instanceof UpdateItem && is_callable($item->callable)) {
                call_user_func_array($item->callable, []);
            }
        }
    }

    /**
     * @return Collection<UpdateItem>
     */
    public function getItems(): Collection
    {
        $items = new ArrayCollection();
        foreach ($this->extenders as $extender) {
            if ($extender instanceof UpdateExtenderInterface) {
                foreach ($extender->getItems() as $item) {
                    if ($item instanceof UpdateItem && is_callable($item->callable)) {
                        $items->add($item);
                    }
                }
            }
        }

        return $items;
    }
}
