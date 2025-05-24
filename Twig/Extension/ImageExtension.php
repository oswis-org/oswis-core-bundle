<?php

/**
 * @noinspection RealpathInStreamContextInspection
 * @noinspection NestedTernaryOperatorInspection
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

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
        /**
         * @var string                         $key
         * @var array{fn: mixed, opts: ?array} $fn
         */
        foreach ($this->getFunctionsArray() as $key => $fn) {
            try {
                $functions[] = new TwigFunction($key, Callback::check($fn['fn']), $fn['opts'] ?? []);
            } catch (InvalidArgumentException) {
            }
        }

        return $functions;
    }

    /**
     * @return  array<string, array{fn: array, opts?: array}>
     */
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
        /**
         * @var string                         $key
         * @var array{fn: mixed, opts?: array} $fn
         */
        foreach ($this->getFunctionsArray() as $key => $fn) {
            try {
                $functions[] = new TwigFilter($key, Callback::check($fn['fn']), $fn['opts'] ?? []);
            } catch (InvalidArgumentException) {
            }
        }

        return $functions;
    }

    public function getImageWidth(?string $imagePath = null): int
    {
        return (int)$this->getImageSize($imagePath)[0];
    }


    /**
     * @param string|null $path
     * @return array{0: int|null, 1: int|null, 2: int|null, 3: string|null, bits?: int|null, channels?: int|null, mime: string|null}|array{null, null, null, null}
     */
    private function getImageSize(?string $path = null): array
    {
        $path = $this->getPath($path);
        if (!empty($path)) {
            $imageSize = (getimagesize($path) ?: [null, null, null, null]);
        } else {
            $imageSize = [null, null, null, null];
        }

        return $imageSize;
    }

    public function getPath(?string $imagePath = null): ?string
    {
        try {
            $path = realpath('../public'.$imagePath) ?: null;

            return $this->fileSystem->exists(''.$path) ? $path : null;
        } catch (IOException) {
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
        if (IMAGETYPE_JPEG !== $this->getImageTypeConstant()) {
            return null;
        }

        return $this->getImageComputedComment($imagePath)
            ?: $this->getImageExifComment($imagePath) ?: $this->getImageIfdComment($imagePath) ?: null;
    }

    public function getImageTypeConstant(?string $imagePath = null): ?int
    {
        $imagePath = $this->getPath($imagePath);
        $imageType = !empty($imagePath) ? (@exif_imagetype($imagePath) ?: null) : null;

        return $imageType ?: $this->getImageSize($imagePath)[2] ?: null;
    }

    public function getImageComputedComment(?string $imagePath = null): ?string
    {
        $imagePath = $this->getPath($imagePath);
        if (!empty($imagePath)) {
            $result = (@exif_read_data($imagePath, 'COMPUTED') ?: null);
        } else {
            $result = null;
        }

        return (is_array($result) && is_array($result['COMPUTED']) && is_string($result['COMPUTED']['UserComment']))
            ? $result['COMPUTED']['UserComment']
            : null;
    }

    public function getImageExifComment(?string $imagePath = null): ?string
    {
        $imagePath = $this->getPath($imagePath);
        if (!empty($imagePath)) {
            $comments = (@exif_read_data($imagePath, 'COMMENT') ?: null);
        } else {
            $comments = null;
        }
        if (!is_array($comments)) {
            return null;
        }

        if (is_array($comments['COMMENT'])) {
            return implode("\n", $comments['COMMENT']);
        }

        return is_string($comments['COMMENT']) ? $comments['COMMENT'] : null;
    }

    public function getImageIfdComment(?string $imagePath = null): ?string
    {
        $imagePath = $this->getPath($imagePath);
        if (!empty($imagePath)) {
            $result = (@exif_read_data($imagePath, 'IFD0') ?: null);
        } else {
            $result = null;
        }

        return (is_array($result) && is_array($result['IFD0']) && is_string(
                $result['IFD0']['UserComment']
            )) ? $result['IFD0']['UserComment'] : null;
    }

    public function getImageMimeType(?string $imagePath = null): ?string
    {
        return @image_type_to_mime_type((int)$this->getImageTypeConstant($imagePath)) ?: null;
    }
}
