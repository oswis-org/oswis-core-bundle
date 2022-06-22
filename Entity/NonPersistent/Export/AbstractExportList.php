<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Definition for PDF export file.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractExportList
{
    public const DEFAULT_TEMPLATE        = null;
    public const DEFAULT_HEADER_TEMPLATE = null;
    public const DEFAULT_FOOTER_TEMPLATE = null;
    public const DEFAULT_TITLE           = 'Export';
    public const DEFAULT_DATA            = ['items' => null];

    public string $title = self::DEFAULT_TITLE;

    public ?string $template = self::DEFAULT_TEMPLATE;

    public ?array $data = self::DEFAULT_DATA;

    public ?string $headerTemplate = self::DEFAULT_HEADER_TEMPLATE;

    public ?string $footerTemplate = self::DEFAULT_FOOTER_TEMPLATE;

    public ?Collection $columns = null;

    public function __construct(string $title = null, ?Collection $columns = null, array $data = null)
    {
        $this->setTitle(''.$title);
        $this->setColumns($columns);
        $this->setData($data);
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): void
    {
        $this->template = $template ?? self::DEFAULT_TEMPLATE;
    }

    public function getHeaderTemplate(): ?string
    {
        return $this->headerTemplate;
    }

    public function setHeaderTemplate(?string $headerTemplate): void
    {
        $this->headerTemplate = $headerTemplate;
    }

    public function getFooterTemplate(): ?string
    {
        return $this->footerTemplate;
    }

    public function setFooterTemplate(?string $footerTemplate): void
    {
        $this->footerTemplate = $footerTemplate;
    }

    public function getContext(): array
    {
        return [
            'title'   => $this->getTitle(),
            'columns' => $this->getColumns(),
            'data'    => $this->getData(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getColumns(): Collection
    {
        return $this->columns ?? new ArrayCollection();
    }

    public function setColumns(?Collection $columns): void
    {
        $this->columns = $columns ?? new ArrayCollection();
    }

    public function getData(): array
    {
        return $this->data ?? self::DEFAULT_DATA;
    }

    public function setData(?array $data): void
    {
        $this->data = $data ?? self::DEFAULT_DATA;
    }
}
