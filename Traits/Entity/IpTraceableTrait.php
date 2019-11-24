<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait IpTraceableTrait
{

    /**
     * @var string|null $createdIp
     *
     * @Gedmo\Mapping\Annotation\IpTraceable(on="create")
     * @Doctrine\ORM\Mapping\Column(length=45, nullable=true)
     * @Symfony\Component\Validator\Constraints\Ip(
     *     message = "Zadaná IP adresa ({{ value }}) není platná.",
     *     version = "all"
     * )
     */
    protected ?string $createdIp;

    /**
     * @var string|null $updatedIp
     *
     * @Gedmo\Mapping\Annotation\IpTraceable(on="update")
     * @Doctrine\ORM\Mapping\Column(length=45, nullable=true)
     * @Symfony\Component\Validator\Constraints\Ip(
     *     message = "Zadaná IP adresa ({{ value }}) není platná.",
     *     version = "all"
     * )
     */
    protected ?string $updatedIp;

    final public function getCreatedIp(): ?string
    {
        return $this->createdIp;
    }

    final public function getUpdatedIp(): ?string
    {
        return $this->updatedIp;
    }
}
