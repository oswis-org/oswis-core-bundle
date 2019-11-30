<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds description field.
 */
trait NationalityTrait
{
    /**
     * Nationality (as national string).
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    protected ?string $nationality = null;

    /**
     * @return string
     */
    final public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     */
    final public function setNationality(?string $nationality): void
    {
        $this->nationality = $nationality;
    }
}
