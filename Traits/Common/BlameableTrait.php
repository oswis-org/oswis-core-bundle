<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

trait BlameableTrait
{
    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="created_author_id", referencedColumnName="id")
     */
    protected ?AppUser $createdAuthor = null;

    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser")
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
