<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

trait BlameableTrait
{
    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="created_author_id", referencedColumnName="id")
     * @Symfony\Component\Serializer\Annotation\MaxDepth(1)
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     properties={"createdAuthor.id": "exact", "createdAuthor.username": "ipartial", "createdAuthor.name": "ipartial"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class,
     *     properties={"createdAuthor.id", "createdAuthor.username", "createdAuthor.name"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     */
    protected ?AppUser $createdBy = null;

    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="updated_author_id", referencedColumnName="id")
     * @Symfony\Component\Serializer\Annotation\MaxDepth(1)
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     properties={"updatedAuthor.id": "exact", "updatedAuthor.username": "ipartial", "updatedAuthor.name": "ipartial"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class,
     *     properties={"updatedAuthor.id", "updatedAuthor.username", "updatedAuthor.name"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     */
    protected ?AppUser $updatedBy = null;

    public function getUpdatedBy(): ?AppUser
    {
        return $this->updatedBy;
    }

    public function getCreatedBy(): ?AppUser
    {
        return $this->createdBy;
    }
}
