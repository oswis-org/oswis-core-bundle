<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Obrázky vložené do textu mailu (spec 2026-09-13 §4.5, dávka 2b).
 *
 * Jen JPG, PNG a GIF; obsah se ověří podle dat (ne podle přípony), obrázek se znovu uloží přes GD — tím
 * zmizí metadata (EXIF s polohou, autorem, fotoaparátem) i cokoli přilepeného za daty — a zmenší se na
 * {@see MAX_SIRKA} px (mail je široký 600 px, dvojnásobek pro ostré displeje). Název je náhodný, takže
 * adresa neprozradí nic o obsahu a nedá se uhodnout. Ukládá se do veřejného adresáře aplikace
 * (`public/uploads/mail-obrazky/<rok>/`): obrázek v mailu musí jít stáhnout bez přihlášení.
 *
 * ⚠️ GIF se ukládá přes GD také — **animace se tím ztratí** (zůstane první snímek). Odpovídá to spec
 * (vše přes GD); kdyby animace byly potřeba, je to samostatné rozhodnutí.
 *
 * Záznam v databázi se nevede (odchylka od spec §4.5 „Vich mapování"): obrázek patří textu mailu,
 * ve kterém je jeho adresa, ne žádné entitě — Vich by potřeboval entitu jen kvůli sobě.
 */
final readonly class MailImageStore
{
    public const int MAX_SIRKA = 1200;
    public const int MAX_BAJTU = 10 * 1024 * 1024;
    public const string ADRESAR = 'uploads/mail-obrazky';

    /** MIME typ podle dat => přípona uloženého souboru. */
    private const array TYPY = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

    public function __construct(private string $publicDir)
    {
    }

    /**
     * Uloží obrázek a vrátí cestu od kořene webu (`/uploads/mail-obrazky/2026/….jpg`) a rozměry.
     *
     * @return array{path: string, width: int, height: int}
     *
     * @throws MailImageException
     */
    public function store(UploadedFile $file): array
    {
        if (!$file->isValid()) {
            throw new MailImageException('Soubor se nepodařilo nahrát: '.$file->getErrorMessage());
        }
        $size = $file->getSize();
        if ($size > self::MAX_BAJTU) {
            throw new MailImageException(sprintf('Obrázek je moc velký (%s MB, nejvýš %d MB).', number_format($size / 1048576, 1, ',', ' '), self::MAX_BAJTU / 1048576));
        }
        $data = (string) file_get_contents($file->getPathname());

        return $this->storeData($data);
    }

    /**
     * @return array{path: string, width: int, height: int}
     *
     * @throws MailImageException
     */
    public function storeData(string $data): array
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($data);
        $pripona = is_string($mime) ? (self::TYPY[$mime] ?? null) : null;
        if (null === $pripona) {
            throw new MailImageException('Do mailu jde vložit jen obrázek JPG, PNG nebo GIF.');
        }
        $obrazek = @imagecreatefromstring($data);
        if (false === $obrazek) {
            throw new MailImageException('Obrázek je poškozený nebo ho nejde přečíst.');
        }
        $obrazek = self::zmensit($obrazek);
        $sirka = imagesx($obrazek);
        $vyska = imagesy($obrazek);
        $adresar = self::ADRESAR.'/'.date('Y');
        $cil = rtrim($this->publicDir, '/').'/'.$adresar;
        if (!is_dir($cil) && !@mkdir($cil, 0775, true) && !is_dir($cil)) {
            throw new MailImageException('Obrázek nejde uložit (adresář pro obrázky není dostupný).');
        }
        $nazev = bin2hex(random_bytes(16)).'.'.$pripona;
        $ok = match ($pripona) {
            'jpg' => imagejpeg($obrazek, $cil.'/'.$nazev, 85),
            'png' => imagepng($obrazek, $cil.'/'.$nazev, 9),
            'gif' => imagegif($obrazek, $cil.'/'.$nazev),
        };
        if (!$ok) {
            throw new MailImageException('Obrázek nejde uložit.');
        }

        return ['path' => '/'.$adresar.'/'.$nazev, 'width' => $sirka, 'height' => $vyska];
    }

    /**
     * Je adresa obrázek nahraný do OSWIS, a pokud ano, existuje ještě jeho soubor? null = cizí adresa.
     * Pozná se podle cesty (hostitel se mezi prostředími liší); tvar cesty je přesný, takže `..` neprojde.
     */
    public function ownImageExists(string $url): ?bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!is_string($path) || 1 !== preg_match('#^/'.preg_quote(self::ADRESAR, '#').'/\d{4}/[0-9a-f]{32}\.(?:jpg|png|gif)$#', $path)) {
            return null;
        }

        return is_file(rtrim($this->publicDir, '/').$path);
    }

    /** Širší než {@see MAX_SIRKA} → zmenšit se zachováním poměru i průhlednosti. */
    private static function zmensit(\GdImage $obrazek): \GdImage
    {
        $sirka = imagesx($obrazek);
        if ($sirka <= self::MAX_SIRKA) {
            return $obrazek;
        }
        $vyska = max(1, (int) round(imagesy($obrazek) * self::MAX_SIRKA / $sirka));
        $mensi = imagecreatetruecolor(self::MAX_SIRKA, $vyska);
        imagealphablending($mensi, false);
        imagesavealpha($mensi, true);
        imagefill($mensi, 0, 0, (int) imagecolorallocatealpha($mensi, 0, 0, 0, 127));
        imagecopyresampled($mensi, $obrazek, 0, 0, 0, 0, self::MAX_SIRKA, $vyska, $sirka, imagesy($obrazek));

        return $mensi;
    }
}
