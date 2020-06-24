<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Abstract image file class for use in uploads and forms.
 * @author       Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractImage extends AbstractFile
{
    /**
     * @Symfony\Component\Validator\Constraints\NotNull()
     * @Vich\UploaderBundle\Mapping\Annotation\UploadableField(
     *     mapping="abstract_image", fileNameProperty="contentName", size="contentSize", mimeType="contentMimeType", dimensions="contentDimensions"
     * )
     */
    protected ?File $file = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $contentDimensions = null;

    public function getContentDimensions(): ?string
    {
        return $this->contentDimensions;
    }

    public function setContentDimensions(?string $contentDimensions): void
    {
        $this->contentDimensions = $contentDimensions;
    }
}
