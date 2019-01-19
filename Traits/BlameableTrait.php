<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Zakjakub\OswisCoreBundle\Entity\AppUser;

trait BlameableTrait
{

    /**
     * Author of first update.
     * @var AppUser
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser")
     * @ORM\JoinColumn(name="created_author_id", referencedColumnName="id")
     */
    protected $createdAuthor;

    /**
     * Author of last update.
     * @var AppUser
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser")
     * @ORM\JoinColumn(name="updated_author_id", referencedColumnName="id")
     */
    protected $updatedAuthor;

    final public function getUpdatedAuthor(): ?AppUser
    {
        return $this->updatedAuthor;
    }

    final public function setUpdatedAuthor(?AppUser $author): void
    {
        $this->updatedAuthor = $author;
    }

    final public function getCreatedAuthor(): ?AppUser
    {
        return $this->createdAuthor;
    }

    final public function setCreatedAuthor(?AppUser $author): void
    {
        $this->createdAuthor = $author;
    }




}
