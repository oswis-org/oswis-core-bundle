<?php
/** @noinspection PhpUnused */

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Form;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\FormValue;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

trait FormValueTrait
{
    /** Form settings - regex for form value (if regex is set, value is allowed). */
    #[Column(type: 'text', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(ExistsFilter::class)]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $formValueRegex = null;

    /** Form settings - value label. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(ExistsFilter::class)]
    #[ApiFilter(OrderFilter::class)]
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
