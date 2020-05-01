<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * ImageExtension.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
final class ImageExtension extends AbstractExtension
{

    public function __construct()
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('image_width', [$this, 'getImageWidth']),
            new TwigFunction('image_path', [$this, 'getImagePath']),
        ];
    }

    public function getImageWidth(string $object, ?string $imagePath = null): ?int
    {
        return 666;
    }

    public function getImagePath(string $object, ?string $imagePath = null): ?int
    {
        $imagePath ??= is_string($object) ? $object : null;

        return $imagePath;
    }

}
