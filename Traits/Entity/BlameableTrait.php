<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\AppUser;

trait BlameableTrait
{

    /**
     * Author of first update.
     * @var AppUser|null
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="created_author_id", referencedColumnName="id")
     */
    protected ?AppUser $createdAuthor;

    /**
     * Author of last update.
     * @var AppUser|null
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="updated_author_id", referencedColumnName="id")
     */
    protected ?AppUser $updatedAuthor;

    final public function getUpdatedAuthor(): ?AppUser
    {
        return $this->updatedAuthor;
    }

    final public function getCreatedAuthor(): ?AppUser
    {
        return $this->createdAuthor;
    }
}
