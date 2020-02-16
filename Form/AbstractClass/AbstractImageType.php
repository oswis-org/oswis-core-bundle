<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Form\AbstractClass;

use Vich\UploaderBundle\Form\Type\VichImageType;

abstract class AbstractImageType extends AbstractFileType
{
    public const VICH_TYPE_CLASS = VichImageType::class;

    public function getBlockPrefix(): string
    {
        return 'oswis_core_abstract_image';
    }
}
