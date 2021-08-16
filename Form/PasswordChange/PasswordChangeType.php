<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Form\PasswordChange;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('password', PasswordType::class, [
            'label'    => 'Nové heslo',
            'help'     => 'Pokud jej nezadáte, bude vygenerováno nové náhodné heslo.',
            'mapped'   => false,
            'required' => true,
        ]);
        $builder->add('submit', SubmitType::class, ['label' => 'ZMĚNIT HESLO']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getBlockPrefix(): string
    {
        return 'core_app_user_password_change';
    }
}
