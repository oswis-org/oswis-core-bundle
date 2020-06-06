<?php /** @noinspection RealpathInStreamContextInspection */
/** @noinspection NestedTernaryOperatorInspection */
/** @noinspection MissingParameterTypeDeclarationInspection */
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use Nette\InvalidArgumentException;
use Nette\Utils\Callback;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * ImageExtension.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
final class ImageExtension extends AbstractExtension
{

    private Filesystem $fileSystem;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    public function getFunctions(): array
    {
        $functions = [];
        foreach ($this->getFunctionsArray() as $key => $fn) {
            try {
                $functions[] = new TwigFunction($key, Callback::check($fn['fn']), $fn['opts'] ?? []);
            } catch (InvalidArgumentException $e) {
            }
        }

        return $functions;
    }

    public function getFunctionsArray(): array
    {
        return [
            'image_width'     => ['fn' => [$this, 'getImageWidth']],
            'image_height'    => ['fn' => [$this, 'getImageHeight']],
            'image_path'      => ['fn' => [$this, 'getImagePath']],
            'image_comment'   => ['fn' => [$this, 'getImageComment']],
            'image_size'      => ['fn' => [$this, 'getImageHtmlSizeAttributes'], 'opts' => ['is_safe' => ['html']]],
            'image_mime_type' => ['fn' => [$this, 'getImageMimeType']],
        ];
    }

    public function getFilters(): array
    {
        $functions = [];
        foreach ($this->getFunctionsArray() as $key => $fn) {
            try {
                $functions[] = new TwigFilter($key, Callback::check($fn['fn']), $fn['opts'] ?? []);
            } catch (InvalidArgumentException $e) {
            }
        }

        return $functions;
    }

    public function getImageWidth(?string $imagePath = null): ?int
    {
        return $this->getImageSize($imagePath)[0];
    }

    private function getImageSize(?string $path = null): array
    {
        $path = $this->getPath($path);

        return $path ? (getimagesize($path) ?: [null, null, null, null]) : [null, null, null, null];
    }

    public function getPath(?string $imagePath = null): ?string
    {
        try {
            $path = realpath('../public'.$imagePath) ?: null;

            return $this->fileSystem->exists($path) ? $path : null;
        } catch (IOException $e) {
            return null;
        }
    }

    public function getImageHeight(?string $imagePath = null): ?int
    {
        return $this->getImageSize($imagePath)[1] ?: null;
    }

    public function getImageHtmlSizeAttributes(?string $imagePath = null): ?string
    {
        return $this->getImageSize($imagePath)[3] ?: null;
    }

    public function getImageComment(?string $imagePath = null): ?string
    {
        return $this->getImageComputedComment($imagePath) ?: $this->getImageExifComment($imagePath) ?: $this->getImageIfdComment($imagePath) ?: null;
    }

    public function getImageComputedComment(?string $imagePath = null): ?string
    {
        $result = @exif_read_data($this->getPath($imagePath), 'COMPUTED');

        return is_array($result) && is_array($result['COMPUTED']) ? $result['COMPUTED']['UserComment'] : null;
    }

    public function getImageExifComment(?string $imagePath = null): ?string
    {
        $comments = @exif_read_data($this->getPath($imagePath), 'COMMENT') ?: null;
        if (!is_array($comments)) {
            return null;
        }

        return is_array($comments['COMMENT']) ? implode("\n", $comments['COMMENT']) : $comments['COMMENT'];
    }

    public function getImageIfdComment(?string $imagePath = null): ?string
    {
        $result = @exif_read_data($this->getPath($imagePath), 'IFD0');

        return $result && is_array($result) && is_array($result['IFD0']) ? $result['IFD0']['UserComment'] : null;
    }

    public function getImageMimeType(?string $imagePath = null): ?string
    {
        return @image_type_to_mime_type($this->getImageTypeConstant($imagePath)) ?: null;
    }

    public function getImageTypeConstant(?string $imagePath = null): ?int
    {
        return @exif_imagetype($this->getPath($imagePath)) ?: $this->getImageSize($imagePath)[2] ?: null;
    }
}
