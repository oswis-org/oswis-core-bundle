<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\HttpFoundation\File\File;
use Zakjakub\OswisCoreBundle\Interfaces\BasicEntityInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

/**
 * Abstract file class for use in uploads and forms.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractFile implements BasicEntityInterface
{
    use BasicEntityTrait;

    /**
     * @Symfony\Component\Validator\Constraints\NotNull()
     * @Vich\UploaderBundle\Mapping\Annotation\UploadableField(
     *     mapping="abstract_file",
     *     fileNameProperty="contentUrl",
     *     mimeType="contentMimeType"
     * )
     */
    public ?File $file = null;

    /**
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     */
    public ?string $contentUrl = null;

    /**
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?int $contentSize = null;

    /**
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?string $contentMimeType = null;

    /**
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?string $contentOriginalName = null;

    public function __toString(): string
    {
        return $this->contentUrl ?? '';
    }
}
