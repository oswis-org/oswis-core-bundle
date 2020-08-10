<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use Exception;

/**
 * Trait adds dateTime field.
 */
trait DateTimeTrait
{
    /**
     * Date and time.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $dateTime = null;

    public function getDaysAgo(?bool $decimal = false): ?float
    {
        try {
            if (null !== $this->getDateTime()) {
                $ago = $this->getDateTime()->diff(new DateTime());

                return !empty($ago) ? (float)($decimal ? $ago->days : $ago->d) : null;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getDateTime(): ?DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(?DateTime $dateTime = null): void
    {
        $this->dateTime = $dateTime ? clone $dateTime : null;
    }
}
