<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use OswisOrg\OswisCoreBundle\Traits\Common\NameableBasicTrait;

trait PersonBasicTrait
{
    use NameableBasicTrait;
    use FullNameTrait;

    public function getName(): ?string
    {
        return $this->getFullName();
    }

    public function setName(?string $name): void
    {
        $this->setFullName($name);
    }

}
