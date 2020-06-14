<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use Doctrine\ORM\Mapping as ORM;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;

/**
 * Twig template for e-mail.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractEMailTemplate
{
    use NameableTrait;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $template = null;

    public function __construct(?Nameable $nameable = null)
    {
        $this->setFieldsFromNameable($nameable);
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): void
    {
        $this->template = $template;
    }
}
