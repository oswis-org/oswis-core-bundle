<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Form\Type;

use OswisOrg\OswisCoreBundle\Mail\Editor\MailEditorConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Text mailu s editorem (spec 2026-09-13 §4.1). Odesílaná hodnota zůstává textarea (Twig + HTML);
 * editor a náhled vykreslí motiv `mail_body_widget` (@OswisOrgOswisCore/form/mail_editor_theme.html.twig).
 *
 * Volby: `preview` (null = bez náhledu; jinak {url, recipients: [{id, label}], subjectField, templateField}),
 * `rows` (výška pole bez JS).
 */
final class MailBodyType extends AbstractType
{
    public function __construct(private readonly MailEditorConfig $editorConfig)
    {
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'mail_body';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['preview' => null, 'rows' => 14]);
        $resolver->setAllowedTypes('preview', ['null', 'array']);
        $resolver->setAllowedTypes('rows', 'int');
    }

    /** @param array<string, mixed> $options */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['editor_config'] = $this->editorConfig->toArray();
        $view->vars['preview'] = $options['preview'];
        $view->vars['rows'] = $options['rows'];
    }
}
