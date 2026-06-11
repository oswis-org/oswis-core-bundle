<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\TwigTemplate;

use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
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
 */
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['entities_get', 'twig_templates_get'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Post(
            denormalizationContext: ['groups' => ['entities_post', 'twig_templates_post'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Get(
            normalizationContext: ['groups' => ['entity_get', 'twig_template_get'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Put(
            denormalizationContext: ['groups' => ['entity_put', 'twig_template_put'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
    filters: ['search'],
    security: "is_granted('ROLE_ADMIN')",
)]
#[SearchAnnotation(['id', 'slug', 'type', 'name'])]
#[Entity(repositoryClass: TwigTemplateRepository::class)]
#[Table(name: 'core_twig_template')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_twig_template')]
class TwigTemplate implements NameableInterface, TextValueInterface
{
    /** Transactional system mail (activation/summary/payment) — usually a file reference. */
    public const KIND_SYSTEM = 'system';

    /** Marketing/campaign mail (infomail/feedback/…) — full Twig in textValue, extends the wrapper. */
    public const KIND_CAMPAIGN = 'campaign';

    /** Reusable body fragment/block inserted into a composed mail (not a complete e-mail). */
    public const KIND_SNIPPET = 'snippet';

    /** Reserved for future non-mail templates (web page / PDF) so they can coexist in this store. */
    public const KIND_PAGE = 'page';

    public const KIND_PDF = 'pdf';

    use NameableTrait;
    use TextValueTrait;

    #[Column(type: 'string', nullable: true)]
    protected ?string $regularTemplateName = null;

    /**
     * Semantic kind of this template (one of the KIND_* constants). Lets the mail config editor
     * group/filter and the bulk composer offer the right templates (campaigns to start from,
     * snippets to insert). Nullable for legacy rows not yet classified.
     */
    #[Column(type: 'string', length: 16, nullable: true)]
    protected ?string $kind = null;

    /** @return list<string> */
    public static function getAllowedKinds(): array
    {
        return [self::KIND_SYSTEM, self::KIND_CAMPAIGN, self::KIND_SNIPPET, self::KIND_PAGE, self::KIND_PDF];
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(?string $kind): void
    {
        $this->kind = (null === $kind || in_array($kind, self::getAllowedKinds(), true)) ? $kind : null;
    }

    public function isCampaign(): bool
    {
        return self::KIND_CAMPAIGN === $this->kind;
    }

    public function isSnippet(): bool
    {
        return self::KIND_SNIPPET === $this->kind;
    }

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
        $updatedAt = $this->getUpdatedAt();

        return $updatedAt?->getTimestamp() <= ($timestamp ?? time());
    }

    final public function getTemplateName(): string
    {
        return $this->getRegularTemplateName() ?? $this->getSlug();
    }
}
