<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\TwigTemplate;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\TextValueInterface;
use OswisOrg\OswisCoreBundle\Repository\TwigTemplateRepository;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TextValueTrait;

/**
 * @author Jakub Zak <mail@jakubzak.eu>
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({
 *     "id",
 *     "slug",
 *     "type",
 *     "name"
 * })
 * @ApiPlatform\Core\Annotation\ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_ADMIN')"
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"entities_get", "twig_templates_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"entities_post", "twig_templates_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"entity_get", "twig_template_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"entity_put", "twig_template_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 */
#[Entity(repositoryClass: TwigTemplateRepository::class)]
#[Table(name: 'core_twig_template')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_twig_template')]
class TwigTemplate implements NameableInterface, TextValueInterface
{
    use NameableTrait;
    use TextValueTrait;

    #[Column(type: 'string', nullable: true)]
    protected ?string $regularTemplateName = null;

    final public function isRegular(): bool
    {
        return (bool)$this->getRegularTemplateName();
    }

    final public function getRegularTemplateName(): ?string
    {
        return $this->regularTemplateName;
    }

    final public function setRegularTemplateName(?string $regularTemplateName): void
    {
        $this->regularTemplateName = $regularTemplateName;
    }

    final public function isFresh(?int $timestamp = null): bool
    {
        return ($updatedAt = $this->getUpdatedAt()) && $updatedAt->getTimestamp() <= ($timestamp ?? time());
    }

    final public function getTemplateName(): ?string
    {
        return $this->getRegularTemplateName() ?? $this->getSlug();
    }
}