<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Form;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\FormValue;

trait FormValueTrait
{
    /**
     * Form settings - regex for form value (if regex is set, value is allowed).
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $formValueRegex = null;

    /**
     * Form settings - value label.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $formValueLabel = null;

    public function isFormValueAllowed(): bool
    {
        return !empty($this->getFormValueRegex());
    }

    public function getFormValueRegex(): ?string
    {
        return $this->formValueRegex;
    }

    public function setFormValueRegex(?string $formValueRegex): void
    {
        $this->formValueRegex = $formValueRegex;
    }

    public function getFormValue(): FormValue
    {
        return new FormValue($this->getFormValueRegex(), $this->getFormValueLabel());
    }

    public function getFormValueLabel(): ?string
    {
        return $this->formValueLabel;
    }

    public function setFormValueLabel(?string $formValueLabel): void
    {
        $this->formValueLabel = $formValueLabel;
    }

    public function setFormValue(FormValue $formValue): void
    {
        $this->setFormValueRegex($formValue->getFormValueRegex());
        $this->setFormValueLabel($formValue->getFormValueLabel());
    }
}
