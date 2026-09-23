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

    /**
     * RODIČ šablony — Twig šablona (soubor, nebo jiná šablona z databáze), ze které tahle vychází.
     * Do zdroje se doplní sám jako `{% extends '…' %}` ({@see slozitZdroj()}), text šablony pak obsahuje
     * jen bloky, které rodiči přepisuje. Prázdný text = mail přesně podle rodiče.
     *
     * ⚠️ Do 23. 9. 2026 pole znamenalo „MÍSTO obsahu": vykreslil se rovnou soubor a text z databáze se
     * nepoužil vůbec (načítač ho dokonce odmítal). Nový význam je pro všech 34 šablon výstupově totožný —
     * měřeno v RodicSablonyVyjdeStejneTest (aplikace). Sloupec si nechává původní jméno.
     */
    #[Column(type: 'string', nullable: true)]
    protected ?string $regularTemplateName = null;

    /**
     * Semantic kind of this template (one of the KIND_* constants). Lets the mail config editor
     * group/filter and the bulk composer offer the right templates (campaigns to start from,
     * snippets to insert). Nullable for legacy rows not yet classified.
     */
    #[Column(type: 'string', length: 16, nullable: true)]
    protected ?string $kind = null;

    /**
     * Předmět e-mailu jako Twig („Infomail – {{ akce }}"), nebo NULL = dosavadní chování: název šablony
     * a za něj název akce, který doplní odesílající služba (23. 9. 2026 — akce v předmětu jako proměnná,
     * ne natvrdo lepená přípona). Bloky předmět nemají.
     */
    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $subject = null;

    /** @return list<string> */
    public static function getAllowedKinds(): array
    {
        return [self::KIND_SYSTEM, self::KIND_CAMPAIGN, self::KIND_SNIPPET, self::KIND_PAGE, self::KIND_PDF];
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $subject = null === $subject ? null : trim($subject);
        $this->subject = '' === $subject ? null : $subject;
    }

    /** Má šablona vlastní předmět? Jinak platí název šablony + akce ({@see getSubject()}). */
    public function hasSubject(): bool
    {
        return null !== $this->subject;
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

    /**
     * Jméno, pod kterým se šablona vykreslí — VŽDY slug, takže ji načte DatabaseLoader a doplní rodiče.
     * (Dřív se u šablony s cestou vracela cesta a vykreslil se rovnou soubor bez obsahu z databáze.)
     */
    final public function getTemplateName(): string
    {
        return $this->getSlug();
    }

    /** Zdroj, jak ho uvidí Twig: rodič z pole + text šablony. */
    final public function getSlozenyZdroj(): string
    {
        return self::slozitZdroj($this->getRegularTemplateName(), $this->getTextValue());
    }

    /**
     * JEDINÉ místo, kde se rodič doplňuje do zdroje — používá ho načítač (i pro otisk cache), kontrola
     * i náhled, aby všichni viděli totéž, co odejde.
     *
     * Když text `extends` už obsahuje (kampaně před převodem, nebo ruční zápis), podruhé se nepřidá:
     * dvojí `extends` by Twig odmítl.
     */
    public static function slozitZdroj(?string $rodic, ?string $text): string
    {
        $text ??= '';
        $rodic = null === $rodic ? '' : trim($rodic);
        if ('' === $rodic || 1 === preg_match('/^\s*\{%-?\s*extends\b/', $text)) {
            return $text;
        }

        return "{% extends '".str_replace("'", "\\'", $rodic)."' %}".$text;
    }
}
