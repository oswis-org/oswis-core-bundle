<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\TwigTemplate;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Verze šablony (dávka 4, spec §6.3): obsah šablony po každém uložení, které ho změnilo — z administrace, API i importu
 * ({@see \OswisOrg\OswisCoreBundle\EventListener\TwigTemplateVersionRecorder}). Obnovení starší verze = nové uložení,
 * tedy nová verze: historie se nikdy nepřepisuje. Autor jako text (uložení může přijít i z konzole).
 */
#[Entity]
#[Table(name: 'core_twig_template_version')]
#[UniqueConstraint(name: 'uniq_twig_template_version_number', columns: ['template_id', 'number'])]
#[Index(name: 'idx_twig_template_version_template', columns: ['template_id'])]
class TwigTemplateVersion
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    protected ?int $id = null;

    #[ManyToOne(targetEntity: TwigTemplate::class)]
    #[JoinColumn(name: 'template_id', nullable: false, onDelete: 'CASCADE')]
    private TwigTemplate $template;

    /** Pořadí verze u šablony (1, 2, 3…). */
    #[Column(type: 'integer')]
    private int $number;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $subject;

    #[Column(name: 'text_value', type: 'text', nullable: true)]
    private ?string $textValue;

    /** Rodič („Vychází z"). */
    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $parent;

    #[Column(type: 'string', length: 16, nullable: true)]
    private ?string $kind;

    #[Column(type: 'string', length: 255)]
    private string $slug;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $author;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $note;

    #[Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(TwigTemplate $template, int $number, ?string $author, ?string $note)
    {
        $this->template = $template;
        $this->number = $number;
        $this->name = $template->getName();
        $this->subject = $template->getSubject();
        $this->textValue = $template->getTextValue();
        $this->parent = $template->getRegularTemplateName();
        $this->kind = $template->getKind();
        $this->slug = $template->getSlug();
        $this->author = $author;
        $this->note = $note;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Otisk obsahu — dvě uložení se stejným otiskem se neliší (verze se nezakládá). Bez autora, poznámky a času.
     */
    public static function otisk(?string $name, ?string $subject, ?string $textValue, ?string $parent, ?string $kind, ?string $slug): string
    {
        return hash('sha256', (string) json_encode([$name, $subject, $textValue, $parent, $kind, $slug]));
    }

    public function getOtisk(): string
    {
        return self::otisk($this->name, $this->subject, $this->textValue, $this->parent, $this->kind, $this->slug);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): TwigTemplate
    {
        return $this->template;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
