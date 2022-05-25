<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

/**
 * Abstract image file class for use in uploads and forms.
 * @author       Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractImage extends AbstractFile
{
    #[NotNull]
    #[UploadableField(mapping: 'abstract_image', fileNameProperty: 'contentName', size: 'contentSize', mimeType: 'contentMimeType')]
    protected ?File $file = null;
}
