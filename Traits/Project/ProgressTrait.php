<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Project;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Trait that adds field related with some progress.
 */
trait ProgressTrait
{
    /** Progress (number between 0.0 and 1.0). */
    #[Column(nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(RangeFilter::class)]
    #[ApiFilter(OrderFilter::class)]
    #[Range(min: 0, max: 1)]
    protected ?float $progress = null;

    /**
     * Get progress as float number between 0.0 and 1.0.
     *
     * @param  bool  $recursive  Sum from children if progress not set.
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

    /** Summed progress from children if progress is not set (to be overwritten). */
    public function getProgressRecursive(): ?float
    {
        return $this->progress;
    }
}
