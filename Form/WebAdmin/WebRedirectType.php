<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Form\WebAdmin;

use OswisOrg\OswisCoreBundle\Entity\Web\WebRedirect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;

final class WebRedirectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'Název (interní popis)',
                'required' => false,
            ])
            ->add('slug', TextType::class, [
                'label'       => 'Slug (část URL)',
                'required'    => true,
                'help'        => 'Veřejná adresa bude /redirect/{slug} a /r/{slug}. Jen malá písmena, číslice a pomlčky.',
                'attr'        => ['placeholder' => 'zpetna-vazba-seznamovak-up-2026-1-turnus', 'style' => 'font-family: monospace;'],
                'constraints' => [
                    new NotBlank(message: 'Slug je povinný.'),
                    new Regex(pattern: '/^[a-z0-9-]+$/', message: 'Slug smí obsahovat jen malá písmena bez diakritiky, číslice a pomlčky.'),
                    new Length(max: 170),
                ],
            ])
            ->add('targetUrl', TextType::class, [
                'label'       => 'Cílová adresa',
                'required'    => true,
                'help'        => 'Absolutní (https://…) nebo relativní v rámci webu (/akce/prihlaska/…).',
                'attr'        => ['placeholder' => 'https://docs.google.com/forms/… nebo /akce/prihlaska/…'],
                'constraints' => [
                    new NotBlank(message: 'Cílová adresa je povinná.'),
                    new Length(max: 2048),
                    // Absolute http(s) URL OR a site-relative path. The path must start with a
                    // single "/" — "//host" is protocol-relative (escapes the site) and is rejected.
                    new AtLeastOneOf(
                        constraints: [
                            new Url(protocols: ['http', 'https'], requireTld: true),
                            new Regex(pattern: '~^/(?!/)~'),
                        ],
                        message: 'Zadej absolutní http(s) adresu, nebo relativní cestu začínající "/".',
                        includeInternalMessages: false,
                    ),
                ],
            ])
            ->add('note', TextType::class, [
                'label'    => 'Poznámka',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Uložit',
                'attr'  => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => WebRedirect::class]);
    }
}
