<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use ApiPlatform\Core\Annotation\ApiProperty;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Abstract file class for use in uploads and forms.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractFile implements BasicInterface
{
    use BasicTrait;

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
