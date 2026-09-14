<?php

/**
 * @noinspection PhpUnused
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class WebMenuItem
{
    public const MAIN_MENU      = 'main-menu';
    public const WEB_ADMIN_MENU = 'web-admin-menu';

    protected string $path;

    protected string $title;

    protected ?string $requiredRole = null;

    protected ?int $priority = null;

    protected bool $newPage = false;

    protected ?Collection $menus = null;

    /**
     * Začátky názvů rout, na kterých je položka aktivní (zvýrazněná). Rozhoduje nejdelší shoda napříč
     * položkami, takže `…_participant_payments` (Platby) přebije `…_participant` (Účastníci).
     *
     * Proč routy a ne adresa: adresy sekcím neodpovídají (Agregace leží pod `/web_admin/prihlasky/…`,
     * detail přihlášky pod `/web_admin/ucastnici/…`, seznam pod `/web_admin/seznam-prihlasek`), takže
     * shoda začátku adresy rozsvítila „Úvod" na většině obrazovek administrace (audit 14. 9. 2026).
     * Prázdný seznam = položka se řídí adresou jako dřív (veřejné menu).
     *
     * @var list<string>
     */
    protected array $activeRoutePrefixes = [];

    /**
     * @param list<string> $activeRoutePrefixes
     */
    public function __construct(
        string $path,
        string $title,
        Collection $menus,
        ?string $requiredRole = null,
        ?int $priority = null,
        bool $newPage = false,
        array $activeRoutePrefixes = [],
    ) {
        $this->menus = $menus;
        $this->path = $path;
        $this->title = $title;
        $this->requiredRole = $requiredRole;
        $this->priority = $priority;
        $this->newPage = $newPage;
        $this->activeRoutePrefixes = $activeRoutePrefixes;
    }

    /**
     * @return list<string>
     */
    public function getActiveRoutePrefixes(): array
    {
        return $this->activeRoutePrefixes;
    }

    /**
     * @param list<string> $activeRoutePrefixes
     */
    public function setActiveRoutePrefixes(array $activeRoutePrefixes): void
    {
        $this->activeRoutePrefixes = $activeRoutePrefixes;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getRequiredRole(): ?string
    {
        return $this->requiredRole;
    }

    public function setRequiredRole(?string $requiredRole): void
    {
        $this->requiredRole = $requiredRole;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    public function isNewPage(): bool
    {
        return $this->newPage;
    }

    public function setNewPage(bool $newPage): void
    {
        $this->newPage = $newPage;
    }

    public function hasMenu(?string $menu): bool
    {
        return empty($menu) || $this->getMenus()->contains($menu);
    }

    /**
     * @return Collection<string>
     */
    public function getMenus(): Collection
    {
        return $this->menus ?? new ArrayCollection();
    }

    /**
     * @param  Collection<string>  $menus
     */
    public function setMenus(Collection $menus): void
    {
        $this->menus = $menus;
    }
}
