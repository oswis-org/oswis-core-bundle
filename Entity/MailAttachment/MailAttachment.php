<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\MailAttachment;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Soubor nahraný ke zprávě (spec e-mailů §5.3 c, dávka 3.5). Leží mimo veřejný web
 * ({@see \OswisOrg\OswisCoreBundle\Mail\Attachment\MailAttachmentStore}); správce ho stáhne po přihlášení.
 *
 * Poslat ho jde dvěma způsoby — o každém souboru rozhodne autor zprávy (rozhodnutí uživatele 7. 10. 2026):
 * jako přílohu, nebo jako odkaz ke stažení. Odkaz je veřejný (kdo ho má, stáhne soubor — jako „kdokoli s odkazem"),
 * proto se soubor zveřejní ({@see zverejnit()}) teprve ve chvíli, kdy odkaz skutečně odchází (zařazení zprávy,
 * zkouška). Do té doby adresa s tokenem nefunguje, i kdyby ji někdo znal.
 */
#[Entity]
#[Table(name: 'core_mail_attachment')]
#[UniqueConstraint(name: 'uniq_mail_attachment_token', columns: ['token'])]
class MailAttachment
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    protected ?int $id = null;

    /** Název, pod kterým soubor uvidí příjemce (z nahraného souboru, bez cesty). */
    #[Column(name: 'original_name', type: 'string', length: 255)]
    private string $originalName;

    /** MIME typ zjištěný z OBSAHU souboru, ne z přípony. */
    #[Column(name: 'mime_type', type: 'string', length: 127)]
    private string $mimeType;

    #[Column(type: 'integer')]
    private int $size;

    #[Column(type: 'string', length: 64)]
    private string $sha256;

    /** Cesta relativně k úložišti příloh (`2026/<hex>.pdf`). */
    #[Column(name: 'storage_path', type: 'string', length: 255)]
    private string $storagePath;

    /** Neuhodnutelný token veřejného odkazu (64 hex znaků). */
    #[Column(type: 'string', length: 64)]
    private string $token;

    /** Odkaz ke stažení funguje (soubor odešel odkazem). */
    #[Column(name: 'is_public', type: 'boolean', options: ['default' => false])]
    private bool $public = false;

    #[Column(name: 'uploaded_by', type: 'string', length: 255, nullable: true)]
    private ?string $uploadedBy;

    #[Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $originalName, string $mimeType, int $size, string $sha256, string $storagePath, ?string $uploadedBy)
    {
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->sha256 = $sha256;
        $this->storagePath = $storagePath;
        $this->uploadedBy = $uploadedBy;
        $this->token = bin2hex(random_bytes(32));
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getSha256(): string
    {
        return $this->sha256;
    }

    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function zverejnit(): void
    {
        $this->public = true;
    }

    public function getUploadedBy(): ?string
    {
        return $this->uploadedBy;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** Velikost pro lidi: „850 kB", „3,2 MB". */
    public function getSizeLabel(): string
    {
        return self::velikost($this->size);
    }

    public static function velikost(int $bajtu): string
    {
        return $bajtu < 1024 * 1024
            ? sprintf('%d kB', max(1, (int) round($bajtu / 1024)))
            : str_replace('.', ',', sprintf('%.1f MB', $bajtu / 1048576));
    }
}
