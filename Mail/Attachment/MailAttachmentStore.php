<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Attachment;

use OswisOrg\OswisCoreBundle\Entity\MailAttachment\MailAttachment;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Úložiště příloh zpráv (spec e-mailů §5.3 c, dávka 3.5).
 *
 * - Mimo veřejný web (`var/mail-prilohy/<rok>/<hex>.<přípona>`): ven se soubor dostane jen přes kontroler —
 *   správci po přihlášení, příjemci odkazem až po zveřejnění ({@see MailAttachment::zverejnit()}).
 * - Typ se pozná podle OBSAHU (ne přípony): PDF, JPG/PNG/GIF, DOCX/XLSX/PPTX, ODT/ODS, TXT. Dokumenty Office a
 *   OpenDocument jsou ZIP — když `finfo` vrátí jen „zip", rozhodne obsah balíku. Makra (docm…) a cokoli jiného neprojde.
 * - Nejvýš {@see MAX_SOUBOR} na soubor; celek příloh jedné zprávy hlídá {@see MAX_CELKEM} (base64 přidá třetinu,
 *   zpráva tak zůstane pod 20 MB, které doporučuje hosting90).
 */
final readonly class MailAttachmentStore
{
    public const int MAX_SOUBOR = 10 * 1024 * 1024;
    public const int MAX_CELKEM = 14 * 1024 * 1024;

    /** MIME typ podle obsahu => přípona. */
    public const array TYPY = [
        'application/pdf'                                                           => 'pdf',
        'image/jpeg'                                                                => 'jpg',
        'image/png'                                                                 => 'png',
        'image/gif'                                                                 => 'gif',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.oasis.opendocument.text'                                   => 'odt',
        'application/vnd.oasis.opendocument.spreadsheet'                            => 'ods',
        'text/plain'                                                                => 'txt',
    ];

    public function __construct(private string $directory)
    {
    }

    /**
     * Ověří a uloží nahraný soubor. Záznam vrátí NEULOŽENÝ (persist dělá volající).
     *
     * @throws MailAttachmentException
     */
    public function store(UploadedFile $file, ?string $uploadedBy): MailAttachment
    {
        if (!$file->isValid()) {
            throw new MailAttachmentException('Soubor se nepodařilo nahrát: '.$file->getErrorMessage());
        }
        $size = (int) $file->getSize();
        if ($size <= 0) {
            throw new MailAttachmentException('Soubor je prázdný.');
        }
        if ($size > self::MAX_SOUBOR) {
            throw new MailAttachmentException(sprintf('Soubor je moc velký (%s, nejvýš %d MB).', MailAttachment::velikost($size), self::MAX_SOUBOR / 1048576));
        }
        $zdroj = $file->getPathname();
        $mime = self::typ($zdroj);
        if (null === $mime) {
            throw new MailAttachmentException('Tenhle druh souboru poslat nejde. Povolené: PDF, obrázek JPG/PNG/GIF, Word/Excel/PowerPoint (DOCX, XLSX, PPTX), OpenDocument (ODT, ODS) a text (TXT).');
        }
        $pripona = self::TYPY[$mime];
        $relativni = date('Y').'/'.bin2hex(random_bytes(16)).'.'.$pripona;
        $cil = $this->absolutePath($relativni);
        $adresar = \dirname($cil);
        if (!is_dir($adresar) && !@mkdir($adresar, 0770, true) && !is_dir($adresar)) {
            throw new MailAttachmentException('Soubor nejde uložit (úložiště příloh není dostupné).');
        }
        if (!@copy($zdroj, $cil)) {
            throw new MailAttachmentException('Soubor nejde uložit.');
        }

        return new MailAttachment(self::nazev($file->getClientOriginalName(), $pripona), $mime, $size, (string) hash_file('sha256', $cil), $relativni, $uploadedBy);
    }

    public function absolutePath(MailAttachment|string $attachment): string
    {
        $relativni = $attachment instanceof MailAttachment ? $attachment->getStoragePath() : $attachment;

        return rtrim($this->directory, '/').'/'.ltrim($relativni, '/');
    }

    /** Typ podle obsahu, nebo null (nepovolený). */
    public static function typ(string $path): ?string
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        // Dokumenty Office a OpenDocument jsou ZIP: rozhodne VŽDY obsah balíku (i když `finfo` typ pozná sám) —
        // jen tak se odhalí makra (docm přejmenovaný na docx `finfo` hlásí jako obyčejný dokument; 7. 10. 2026).
        if (in_array($mime, ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'], true)
            || (is_string($mime) && (str_starts_with($mime, 'application/vnd.openxmlformats-') || str_starts_with($mime, 'application/vnd.oasis.opendocument.') || str_starts_with($mime, 'application/vnd.ms-')))) {
            return self::typBaliku($path);
        }

        return is_string($mime) && isset(self::TYPY[$mime]) ? $mime : null;
    }

    /** Office Open XML / OpenDocument podle obsahu ZIP balíku. */
    private static function typBaliku(string $path): ?string
    {
        $zip = new \ZipArchive();
        if (true !== $zip->open($path, \ZipArchive::RDONLY)) {
            return null;
        }
        try {
            $mimetype = $zip->getFromName('mimetype');
            if (is_string($mimetype)) {
                $mimetype = trim($mimetype);
                // Makra OpenDocument (Basic/, Scripts/) neprojdou.
                for ($i = 0; $i < $zip->numFiles; ++$i) {
                    $nazev = (string) $zip->getNameIndex($i);
                    if (str_starts_with($nazev, 'Basic/') || str_starts_with($nazev, 'Scripts/')) {
                        return null;
                    }
                }

                return in_array($mimetype, ['application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet'], true) ? $mimetype : null;
            }
            if (false === $zip->locateName('[Content_Types].xml')) {
                return null;
            }
            // Makra (vbaProject.bin) neprojdou ani v balíku s „bezpečnou" příponou.
            if (false !== $zip->locateName('word/vbaProject.bin') || false !== $zip->locateName('xl/vbaProject.bin') || false !== $zip->locateName('ppt/vbaProject.bin')) {
                return null;
            }

            return match (true) {
                false !== $zip->locateName('word/document.xml')     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                false !== $zip->locateName('xl/workbook.xml')       => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                false !== $zip->locateName('ppt/presentation.xml')  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                default                                             => null,
            };
        } finally {
            $zip->close();
        }
    }

    /**
     * Název pro příjemce: bez cesty a řídicích znaků, nejvýš 200 znaků, s příponou podle SKUTEČNÉHO typu
     * („program.exe" s PDF uvnitř odejde jako „program.pdf").
     */
    public static function nazev(string $original, string $pripona): string
    {
        $nazev = trim((string) preg_replace('/[\x00-\x1F\x7F\/\\\\]+/u', ' ', basename(str_replace('\\', '/', $original))));
        $zaklad = (string) preg_replace('/\.[^.]{1,8}$/u', '', $nazev);
        $soucasna = mb_strtolower((string) pathinfo($nazev, PATHINFO_EXTENSION));
        if ('jpg' === $pripona && 'jpeg' === $soucasna) {
            $pripona = $soucasna;
        }
        $zaklad = trim(mb_substr('' === trim($zaklad) ? 'priloha' : $zaklad, 0, 190));

        return $zaklad.'.'.$pripona;
    }
}
