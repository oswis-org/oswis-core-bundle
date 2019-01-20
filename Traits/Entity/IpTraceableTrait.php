<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IpTraceableTrait
{

    /**
     * @var string|null $createdIp
     *
     * @Gedmo\Mapping\Annotation\IpTraceable(on="create")
     * @ORM\Column(length=45, nullable=true)
     * @Symfony\Component\Validator\Constraints\Ip(
     *     message = "Zadaná IP adresa ({{ value }}) není platná.",
     *     version = "all"
     * )
     */
    protected $createdIp;

    /**
     * @var string|null $updatedIp
     *
     * @Gedmo\Mapping\Annotation\IpTraceable(on="update")
     * @ORM\Column(length=45, nullable=true)
     * @Symfony\Component\Validator\Constraints\Ip(
     *     message = "Zadaná IP adresa ({{ value }}) není platná.",
     *     version = "all"
     * )
     */
    protected $updatedIp;

    final public function getCreatedIp(): ?string
    {
        return $this->createdIp;
    }

    final public function getUpdatedIp(): ?string
    {
        return $this->updatedIp;
    }


}
