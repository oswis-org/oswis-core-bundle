<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\HttpFoundation\File\File;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

/**
 * Abstract file class for use in uploads and forms.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractFile
{
    use BasicEntityTrait;

    /**
     * @var File|null
     * @Symfony\Component\Validator\Constraints\NotNull()
     * @Vich\UploaderBundle\Mapping\Annotation\UploadableField(
     *     mapping="abstract_file",
     *     fileNameProperty="contentUrl",
     *     mimeType="contentMimeType"
     * )
     */
    public ?File $file = null;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     */
    public ?string $contentUrl = null;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?int $contentSize = null;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?string $contentMimeType = null;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?string $contentOriginalName = null;

    /** @noinspection MethodShouldBeFinalInspection */
    public function __toString(): string
    {
        return $this->contentUrl ?? '';
    }
}
