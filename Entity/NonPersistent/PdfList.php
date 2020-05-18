<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

/**
 * Definition for PDF export file.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class PdfList
{
    public const DEFAULT_TEMPLATE = '@OswisOrgOswisCore/documents/pdf-document.html.twig';
    public const DEFAULT_HEADER_TEMPLATE = '@OswisOrgOswisCore/documents/parts/header.html.twig';
    public const DEFAULT_FOOTER_TEMPLATE = '@OswisOrgOswisCore/documents/parts/footer.html.twig';
    public const DEFAULT_PAPER_FORMAT = 'A4';
    public const DEFAULT_PAPER_LANDSCAPE = false;
    public const DEFAULT_TITLE = 'Export';
    public const DEFAULT_DATA = [];

    public string $title = self::DEFAULT_TITLE;

    public string $template = self::DEFAULT_TEMPLATE;

    public array $data = [];

    public string $format = self::DEFAULT_PAPER_FORMAT;

    public bool $landscape = self::DEFAULT_PAPER_LANDSCAPE;

    public ?string $headerTemplate = self::DEFAULT_HEADER_TEMPLATE;

    public ?string $footerTemplate = self::DEFAULT_FOOTER_TEMPLATE;

    public ?Collection $columns = null;

    public function __construct(string $title = null, ?Collection $columns = null)
    {
        $this->setTitle($title);
        $this->setColumns($columns);
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
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
            'title' => $this->getTitle(),
            'data'  => $this->getData(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title ?? self::DEFAULT_TITLE;
    }

    public function getData(): array
    {
        return $this->data ?? [];
    }

    public function setData(array $data): void
    {
        $this->data = $data ?? self::DEFAULT_DATA;
    }

    public function getColumns(): Collection
    {
        return $this->columns ?? new ArrayCollection();
    }

    public function setColumns(?Collection $columns): void
    {
        $this->columns = $columns ?? new ArrayCollection();
    }

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    public function getMPdf(): Mpdf
    {
        $mPdf = new Mpdf(['format' => $this->getFormatString(), 'mode' => 'utf-8']);
        $mPdf->SetTitle($this->getTitle());
        $mPdf->SetSubject($this->getTitle());
        $mPdf->h2toc = ['H1' => 0, 'H2' => 1, 'H3' => 2, 'H4' => 3, 'H5' => 4, 'H6' => 5];
        $mPdf->showImageErrors = true;
        $mPdf->useSubstitutions = true;

        return $mPdf;
    }

    public function getFormatString(): string
    {
        return $this->getFormat().($this->isLandscape() ? '-L' : '');
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format ?? self::DEFAULT_PAPER_FORMAT;
    }

    public function isLandscape(): bool
    {
        return $this->landscape;
    }

    public function setLandscape(bool $landscape): void
    {
        $this->landscape = $landscape ?? self::DEFAULT_PAPER_LANDSCAPE;
    }

}
