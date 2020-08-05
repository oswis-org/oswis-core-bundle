<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Form\Activation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivationRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'username',
            TextType::class,
            [
                'label'    => 'Uživatelské jméno nebo e-mail',
                'help'     => 'Zadejte uživatelské jméno nebo e-mail uvedený u uživatelského účtu.',
                'mapped'   => false,
                'required' => true,
            ]
        );
        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'POŽÁDAT O AKTIVACI',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getBlockPrefix(): string
    {
        return 'core_app_user_activation_request';
    }
}
