<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Form\AbstractClass;

use OswisOrg\OswisCoreBundle\Utils\FileUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

abstract class AbstractFileType extends AbstractType
{
    public const VICH_TYPE_CLASS = VichFileType::class;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $maxSize = FileUtils::humanReadableFileUploadMaxSize();
        $maxSize = $maxSize ? " (max. $maxSize)" : '';
        $builder->add('file', self::VICH_TYPE_CLASS, [
            'label'          => false,
            'download_label' => true,
            'download_uri'   => true,
            'required'       => false,
            'attr'           => [
                'placeholder' => "Kliknutím vyberte soubor$maxSize...",
            ],
        ]);
    }

    /**
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => $this::getFileClassName(),
            'csrf_protection' => false,
        ]);
    }

    abstract public static function getFileClassName(): string;

    public function getBlockPrefix(): string
    {
        return 'oswis_core_abstract_file';
    }
}
