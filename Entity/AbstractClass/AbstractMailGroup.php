<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailCategoryInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailGroupInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\DateRangeTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\PriorityTrait;

/**
 * Mail group contains restrictions of recipients.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractMailGroup implements MailGroupInterface
{
    use NameableTrait;
    use PriorityTrait;
    use DateRangeTrait;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected ?TwigTemplate $twigTemplate = null;

    /**
     * @var bool Automatic mailing message that is sent to all recipients (restricted by restrictions).
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected bool $automaticMailing = false;

    protected ?MailCategoryInterface $category = null;

    public function __construct(
        ?Nameable $nameable = null,
        ?int $priority = null,
        ?DateTimeRange $range = null,
        ?MailCategoryInterface $category = null,
        ?TwigTemplate $twigTemplate = null,
        bool $automaticMailing = false
    ) {
        $this->setFieldsFromNameable($nameable);
        $this->setPriority($priority);
        $this->setDateTimeRange($range);
        $this->setCategory($category);
        $this->setTwigTemplate($twigTemplate);
        $this->setAutomaticMailing($automaticMailing);
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

    public function isApplicableByRestrictions(?object $entity): bool
    {
        assert($entity);

        return true;
    }

    public function isCategory(?AbstractMailCategory $category): bool
    {
        return $this->getCategory() === $category;
    }

    public function getCategory(): ?MailCategoryInterface
    {
        return $this->category;
    }

    public function setCategory(?MailCategoryInterface $category): void
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

    public function isAutomaticMailing(): bool
    {
        return $this->automaticMailing;
    }

    public function setAutomaticMailing(bool $automaticMailing): void
    {
        $this->automaticMailing = $automaticMailing;
    }
}
