<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use Exception;

/**
 * Trait ConfirmedByUserTrait.
 */
trait ConfirmedByUserTrait
{
    /**
     * Date and time of user confirmation.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $confirmedByUser = null;

    public function isConfirmedByUser(): bool
    {
        return $this->confirmedByUser ? true : false;
    }

    public function getConfirmedByUser(): ?DateTime
    {
        return $this->confirmedByUser;
    }

    public function setConfirmedByUser(?DateTime $confirmedByUser): void
    {
        $this->confirmedByUser = $confirmedByUser;
    }

    /**
     * @throws Exception
     */
    public function confirmByUser(): void
    {
        if (!$this->confirmedByUser) {
            $this->confirmedByUser = new DateTime();
        }
    }
}
