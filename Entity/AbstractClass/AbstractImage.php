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
     *     mapping="abstract_image", fileNameProperty="contentName", size="contentSize", mimeType="contentMimeType",
     *     dimensions={"contentDimensionsWidth", "contentDimensionsHeight"}
     * )
     */
    protected ?File $file = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $contentDimensionsWidth = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $contentDimensionsHeight = null;

    public function getContentDimensionsWidth(): ?int
    {
        return $this->contentDimensionsWidth;
    }

    public function setContentDimensionsWidth(?int $contentDimensionsWidth): void
    {
        $this->contentDimensionsWidth = $contentDimensionsWidth;
    }

    public function getContentDimensionsHeight(): ?int
    {
        return $this->contentDimensionsHeight;
    }

    public function setContentDimensionsHeight(?int $contentDimensionsHeight): void
    {
        $this->contentDimensionsHeight = $contentDimensionsHeight;
    }

    public function setDimensions(?int $width, ?int $height): void
    {
        $this->setContentDimensionsWidth($width);
        $this->setContentDimensionsHeight($height);
    }
}
