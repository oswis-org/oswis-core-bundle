<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\AppUser;

trait BlameableTrait
{
    use \Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;

    public function getUpdatedAuthor(): ?AppUser
    {
        return $this->getUpdatedBy();
    }

    public function getCreatedAuthor(): ?AppUser
    {
        return $this->getCreatedBy();
    }
}
