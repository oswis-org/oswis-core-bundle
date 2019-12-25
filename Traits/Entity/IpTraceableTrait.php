<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait IpTraceableTrait
{
    /**
     * @var string|null
     *
     * @Gedmo\Mapping\Annotation\IpTraceable(on="create")
     * @Doctrine\ORM\Mapping\Column(length=45, nullable=true)
     * @Symfony\Component\Validator\Constraints\Ip(
     *     message = "Zadaná IP adresa ({{ value }}) není platná.",
     *     version = "all"
     * )
     */
    protected ?string $createdIp = null;

    /**
     * @var string|null
     *
     * @Gedmo\Mapping\Annotation\IpTraceable(on="update")
     * @Doctrine\ORM\Mapping\Column(length=45, nullable=true)
     * @Symfony\Component\Validator\Constraints\Ip(
     *     message = "Zadaná IP adresa ({{ value }}) není platná.",
     *     version = "all"
     * )
     */
    protected ?string $updatedIp = null;

    public function getCreatedIp(): ?string
    {
        return $this->createdIp;
    }

    public function getUpdatedIp(): ?string
    {
        return $this->updatedIp;
    }
}
