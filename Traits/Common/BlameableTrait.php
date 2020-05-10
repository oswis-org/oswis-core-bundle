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
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="created_author_id", referencedColumnName="id")
     * @Symfony\Component\Serializer\Annotation\MaxDepth(1)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     */
    protected ?AppUser $createdAuthor = null;

    /**
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser")
     * @Doctrine\ORM\Mapping\JoinColumn(name="updated_author_id", referencedColumnName="id")
     * @Symfony\Component\Serializer\Annotation\MaxDepth(1)
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     strategy="exact",
     *     properties={"updatedAuthor.id", "updatedAuthor.username", "updatedAuthor.username"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
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
