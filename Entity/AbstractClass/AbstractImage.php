<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Abstract image file class for use in uploads and forms.
 * @author       Jakub Zak <mail@jakubzak.eu>
 * @noinspection ClassNameCollisionInspection
 */
abstract class AbstractImage extends AbstractFile
{
    /**
     * @Symfony\Component\Validator\Constraints\NotNull()
     * @Vich\UploaderBundle\Mapping\Annotation\UploadableField(
     *     mapping="abstract_image",
     *     fileNameProperty="contentUrl",
     *     dimensions={"contentDimensionsWidth", "contentDimensionsHeight"},
     *     mimeType="contentMimeType"
     * )
     */
    public ?File $file = null;

    /**
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?int $contentDimensionsWidth = null;

    /**
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public ?int $contentDimensionsHeight = null;
}
