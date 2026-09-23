<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Form\Type;

use OswisOrg\OswisCoreBundle\Mail\Editor\MailEditorConfig;
use OswisOrg\OswisCoreBundle\Mail\Rendering\MailRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Předmět mailu (Twig) s nabídkou „Vložit údaj" — stejné proměnné jako v textu mailu.
 *
 * PROČ: od 23. 9. 2026 má šablona vlastní předmět a akce v něm je proměnná (`{{ akce }}`), ne natvrdo
 * lepená přípona. Psát `{{ … }}` ručně je past; nabídka vloží výraz na místo kurzoru. Hodnota zůstává
 * obyčejné textové pole — motiv `mail_subject_widget` (@OswisOrgOswisCore/form/mail_editor_theme.html.twig).
 */
final class MailSubjectType extends AbstractType
{
    public function __construct(private readonly MailEditorConfig $editorConfig)
    {
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'mail_subject';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['attr' => ['maxlength' => MailRenderer::SUBJECT_MAX_LENGTH]]);
    }

    /** @param array<string, mixed> $options */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['variable_groups'] = $this->editorConfig->toArray()['variableGroups'];
    }
}
