<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailGroupInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\DateRangeTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\PriorityTrait;

/**
 * Mail group contains restrictions of recipients for some messages.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractMailGroup implements MailGroupInterface
{
    use NameableTrait;
    use PriorityTrait;
    use DateRangeTrait;

    #[ManyToOne(targetEntity: TwigTemplate::class, fetch: 'EAGER')]
    #[JoinColumn(nullable: true)]
    protected ?TwigTemplate $twigTemplate = null;

    /**
     * @var bool Automatic mailing message that is sent to all recipients (restricted by restrictions).
     */
    #[Column(type: 'boolean', nullable: false)]
    protected bool $automaticMailing = false;

    public function __construct(
        ?Nameable $nameable = null,
        ?int $priority = null,
        ?DateTimeRange $range = null,
        ?TwigTemplate $twigTemplate = null,
        bool $automaticMailing = false
    ) {
        $this->setFieldsFromNameable($nameable);
        $this->setPriority($priority);
        $this->setDateTimeRange($range);
        $this->setTwigTemplate($twigTemplate);
        $this->setAutomaticMailing($automaticMailing);
    }

    public function getTemplateName(): ?string
    {
        return $this->getTwigTemplate()?->getTemplateName();
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

    /** @noinspection PhpUnusedParameterInspection */
    public function isApplicableByDate(?DateTime $dateTime = null): bool
    {
        return $this->isInDateRange($dateTime ?? new DateTime());
    }

    public function isApplicableByRestrictions(?object $entity): bool
    {
        return (bool)$entity;
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
