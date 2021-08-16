<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

class FormValue
{
    public ?string $formValueRegex = null;

    public ?string $formValueLabel = null;

    public function __construct(?string $formValueRegex, ?string $formValueLabel)
    {
        $this->formValueRegex = $formValueRegex;
        $this->formValueLabel = $formValueLabel;
    }

    public function getFormValueRegex(): ?string
    {
        return $this->formValueRegex;
    }

    public function setFormValueRegex(?string $formValueRegex): void
    {
        $this->formValueRegex = $formValueRegex;
    }

    public function getFormValueLabel(): ?string
    {
        return $this->formValueLabel;
    }

    public function setFormValueLabel(?string $formValueLabel): void
    {
        $this->formValueLabel = $formValueLabel;
    }
}
