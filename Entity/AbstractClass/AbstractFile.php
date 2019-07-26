<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\HttpFoundation\File\File;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

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
    public $file;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     */
    public $contentUrl;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public $contentSize;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public $contentMimeType;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    public $contentOriginalName;

    /** @noinspection MethodShouldBeFinalInspection */
    public function __toString(): string
    {
        return $this->contentUrl ?? '';
    }

}