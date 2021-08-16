<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

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
     *     mapping="abstract_image", fileNameProperty="contentName", size="contentSize", mimeType="contentMimeType"
     * )
     */
    protected ?File $file = null;
}
