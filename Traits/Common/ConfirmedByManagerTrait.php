<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use Exception;

/**
 * Trait ConfirmedByManagerTrait.
 */
trait ConfirmedByManagerTrait
{
    /**
     * Confirmed by manager.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $confirmedByManager = null;

    public function isConfirmedByManager(): bool
    {
        return $this->confirmedByManager ? true : false;
    }

    public function getConfirmedByManager(): ?DateTime
    {
        return $this->confirmedByManager;
    }

    public function setConfirmedByManager(?DateTime $confirmedByUser): void
    {
        $this->confirmedByManager = $confirmedByUser;
    }

    /**
     * @throws Exception
     */
    public function confirmByManager(): void
    {
        if (!$this->confirmedByManager) {
            $this->confirmedByManager = new DateTime();
        }
    }
}
