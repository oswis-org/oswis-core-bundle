<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\AppUser;

trait BlameableTrait
{
    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="created_author_id", referencedColumnName="id")
     */
    protected ?AppUser $createdAuthor = null;

    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="updated_author_id", referencedColumnName="id")
     */
    protected ?AppUser $updatedAuthor = null;

    public function getUpdatedAuthor(): ?AppUser
    {
        return $this->updatedAuthor;
    }

    public function getCreatedAuthor(): ?AppUser
    {
        return $this->createdAuthor;
    }
}
