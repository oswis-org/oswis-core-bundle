<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\MailTestInbox;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Zkušební schránka (spec e-mailů §5.3 a, Nastavení sekce Komunikace): adresa, na kterou smí odejít „[ZKOUŠKA]"
 * rozepsané zprávy — vedle adresy přihlášeného správce. Zkouška nikdy nejde na libovolnou adresu: seznam spravuje
 * tým, aby se zkouška omylem nedostala k účastníkovi a aby šlo ověřit vzhled u více poskytovatelů (Seznam, Gmail…).
 */
#[Entity]
#[Table(name: 'core_mail_test_inbox')]
#[UniqueConstraint(name: 'uniq_mail_test_inbox_email', columns: ['email'])]
class MailTestInbox
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    protected ?int $id = null;

    #[Column(type: 'string', length: 255)]
    private string $email;

    /** K čemu schránka je (např. „Seznam", „Outlook UP"). */
    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $note;

    #[Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $email, ?string $note = null)
    {
        $this->email = mb_strtolower(trim($email));
        $note = null !== $note ? trim($note) : null;
        $this->note = '' === $note ? null : $note;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
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
