<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class WebMenuItem
{
    public const MAIN_MENU = 'main-menu';
    public const WEB_ADMIN_MENU = 'web-admin-menu';

    protected string $path;

    protected string $title;

    protected ?string $requiredRole = null;

    protected ?int $priority = null;

    protected bool $newPage = false;

    protected ?Collection $menus = null;

    public function __construct(
        string $path,
        string $title,
        Collection $menus,
        ?string $requiredRole = null,
        ?int $priority = null,
        bool $newPage = false
    ) {
        $this->menus = $menus;
        $this->path = $path;
        $this->title = $title;
        $this->requiredRole = $requiredRole;
        $this->priority = $priority;
        $this->newPage = $newPage;
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
     * @param Collection<string> $menus
     */
    public function setMenus(Collection $menus): void
    {
        $this->menus = $menus;
    }
}
