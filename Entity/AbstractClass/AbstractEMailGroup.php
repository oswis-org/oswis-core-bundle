<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Interfaces\EMail\EMailCategoryInterface;
use OswisOrg\OswisCoreBundle\Interfaces\EMail\EMailGroupInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\DateRangeTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\PriorityTrait;

/**
 * EMail group contains restrictions of recipients.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractEMailGroup implements EMailGroupInterface
{
    use NameableTrait;
    use PriorityTrait;
    use DateRangeTrait;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected ?TwigTemplate $twigTemplate = null;

    protected ?EMailCategoryInterface $category = null;

    public function __construct(
        ?Nameable $nameable = null,
        ?int $priority = null,
        ?DateTimeRange $range = null,
        ?EMailCategoryInterface $category = null,
        ?TwigTemplate $twigTemplate = null
    ) {
        $this->setFieldsFromNameable($nameable);
        $this->setPriority($priority);
        $this->setDateTimeRange($range);
        $this->setCategory($category);
        $this->setTwigTemplate($twigTemplate);
    }


    public function getTemplateName(): ?string
    {
        return $this->getTwigTemplate() ? $this->getTwigTemplate()->getTemplateName() : null;
    }

    public function getTwigTemplate(): ?TwigTemplate
    {
        return $this->twigTemplate;
    }

    public function setTwigTemplate(?TwigTemplate $template): void
    {
        $this->twigTemplate = $template;
    }

    public function isApplicable(?object $entity): bool
    {
        return $this->isApplicableByDate() && $this->isApplicableByRestrictions($entity);
    }

    public function isApplicableByDate(?DateTime $dateTime = null): bool
    {
        return $this->isInDateRange($dateTime ?? new DateTime());
    }

    abstract public function isApplicableByRestrictions(?object $entity): bool;

    public function isCategory(?AbstractEMailCategory $category): bool
    {
        return $this->getCategory() === $category;
    }

    public function getCategory(): ?EMailCategoryInterface
    {
        return $this->category;
    }

    public function setCategory(?EMailCategoryInterface $category): void
    {
        $this->category = $category;
    }

    public function isType(?string $type): bool
    {
        return $this->getType() === $type;
    }

    public function getType(): ?string
    {
        return $this->getCategory() ? $this->getCategory()->getType() : null;
    }
}
