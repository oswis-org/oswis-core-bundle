<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait that adds field related with some progress.
 */
trait ProgressTrait
{
    /**
     * Progress (number between 0.0 and 1.0).
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?float $progress = null;

    /**
     * Get progress as float number between 0.0 and 1.0.
     *
     * @param bool $recursive Sum from children if progress not set.
     *
     * @return float|null
     */
    public function getProgress(bool $recursive = true): ?float
    {
        return $recursive ? $this->getProgressRecursive() : $this->progress;
    }

    public function setProgress(?float $progress): void
    {
        $progress = $progress < 0.0 ? 0.0 : $progress;
        $progress = $progress > 1.0 ? 1.0 : $progress;
        if ($this->getProgress() !== $progress) {
            $this->progress = $progress;
        }
    }

    /**
     * Summed progress from children if progress is not set (to be overwritten).
     * @return float|null
     */
    public function getProgressRecursive(): ?float
    {
        return $this->progress;
    }
}
