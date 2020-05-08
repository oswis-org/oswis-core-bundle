<?php /** @noinspection RealpathInStreamContextInspection */
/** @noinspection NestedTernaryOperatorInspection */
/** @noinspection MissingParameterTypeDeclarationInspection */
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

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
        return [
            new TwigFunction('image_width', [$this, 'getImageWidth']),
            new TwigFunction('image_height', [$this, 'getImageHeight']),
            new TwigFunction('image_path', [$this, 'getImagePath']),
            new TwigFunction('image_comment', [$this, 'getImageComment']),
            new TwigFunction('image_size', [$this, 'getImageHtmlSizeAttributes'], ['is_safe' => ['html']]),
            new TwigFunction('image_mime_type', [$this, 'getImageMimeType']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('image_width', [$this, 'getImageWidth']),
            new TwigFilter('image_height', [$this, 'getImageHeight']),
            new TwigFilter('image_path', [$this, 'getImagePath']),
            new TwigFilter('image_comment', [$this, 'getImageComment']),
            new TwigFilter('image_size', [$this, 'getImageHtmlSizeAttributes'], ['is_safe' => ['html']]),
            new TwigFilter('image_mime_type', [$this, 'getImageMimeType']),
        ];
    }

    public function getImageWidth($object, ?string $imagePath = null): ?int
    {
        return $this->getImageSize($object, $imagePath)[0] ?: null;
    }

    private function getImageSize($object, ?string $path = null): array
    {
        $path = $this->getCorrectRelativePath($object, $path);
        try {
            return $this->fileSystem->exists($path) ? (getimagesize($path) ?: [null, null, null, null]) : [null, null, null, null];
        } catch (IOException $e) {
            return [null, null, null, null];
        }
    }

    public function getCorrectRelativePath($object, ?string $imagePath = null): string
    {
        return realpath('../public'.(is_string($object) ? $object : $imagePath));
    }

    public function getImageHeight($object, ?string $imagePath = null): ?int
    {
        return $this->getImageSize($object, $imagePath)[1] ?: null;
    }

    public function getImageHtmlSizeAttributes($object, ?string $imagePath = null): ?string
    {
        return $this->getImageSize($object, $imagePath)[3] ?: null;
    }

    public function getImageExifComment($object, ?string $imagePath = null): ?string
    {
        $comments = @exif_read_data($this->getCorrectRelativePath($object, $imagePath), 'COMMENT')['COMMENT'];

        return is_array($comments) ? implode("\n", $comments) : $comments;
    }

    public function getImageComment($object, ?string $imagePath = null): ?string
    {
        return $this->getImageComputedComment($object, $imagePath) /*?: $this->getImageExifComment($object, $imagePath)*/ ?: $this->getImageIfdComment($object, $imagePath) ?: null;
    }

    public function getImageComputedComment($object, ?string $imagePath = null): ?string
    {
        return @exif_read_data($this->getCorrectRelativePath($object, $imagePath), 'COMPUTED')['COMPUTED']['UserComment'];
    }

    public function getImageIfdComment($object, ?string $imagePath = null): ?string
    {
        return @exif_read_data($this->getCorrectRelativePath($object, $imagePath), 'IFD0')['IFD0']['UserComment'];
    }

    public function getImageMimeType($object, ?string $imagePath = null): ?string
    {
        return @image_type_to_mime_type($this->getImageTypeConstant($object, $imagePath)) ?: null;
    }

    public function getImageTypeConstant($object, ?string $imagePath = null): ?int
    {
        return @exif_imagetype($this->getCorrectRelativePath($object, $imagePath)) ?: $this->getImageSize($object, $imagePath)[2] ?: null;
    }
}
