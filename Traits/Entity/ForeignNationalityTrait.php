<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds foreignNationality boolean field.
 */
trait ForeignNationalityTrait
{
    /**
     * Foreign nationality.
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $foreignNationality = null;

    final public function getForeignNationality(): bool
    {
        return $this->foreignNationality ?? false;
    }

    /**
     * @param bool $foreignNationality
     */
    final public function setForeignNationality(?bool $foreignNationality): void
    {
        $this->foreignNationality = $foreignNationality;
    }
}
