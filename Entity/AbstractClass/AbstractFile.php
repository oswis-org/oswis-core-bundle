<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use ApiPlatform\Core\Annotation\ApiProperty;
use DateTime;
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
     *     mapping="abstract_file", size="contentSize", fileNameProperty="contentName", mimeType="contentMimeType"
     * )
     */
    protected ?File $file = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     */
    protected ?string $contentName = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $contentSize = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $contentMimeType = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $contentOriginalName = null;

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
        if (null !== $file) {
            $this->updatedAt = new DateTime();
        }
    }

    public function getContentName(): ?string
    {
        return $this->contentName;
    }

    public function setContentName(?string $contentName): void
    {
        $this->contentName = $contentName;
    }

    public function getContentSize(): ?string
    {
        return $this->contentSize;
    }

    public function setContentSize(?string $contentSize): void
    {
        $this->contentSize = $contentSize;
    }

    public function getContentMimeType(): ?string
    {
        return $this->contentMimeType;
    }

    public function setContentMimeType(?string $contentMimeType): void
    {
        $this->contentMimeType = $contentMimeType;
    }

    public function getContentOriginalName(): ?string
    {
        return $this->contentOriginalName;
    }

    public function setContentOriginalName(?string $contentOriginalName): void
    {
        $this->contentOriginalName = $contentOriginalName;
    }

    public function __toString(): string
    {
        return $this->contentName ?? '';
    }
}
