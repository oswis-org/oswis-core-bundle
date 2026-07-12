<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\AbstractClass;

use ApiPlatform\Validator\Exception\ValidationException;
use Doctrine\Persistence\ManagerRegistry;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractFile;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractFileAction
{
    public function __construct(
        protected ManagerRegistry $doctrine,
        protected FormFactoryInterface $factory,
        protected ValidatorInterface $validator
    ) {
    }

    /**
     * @param Request $request
     *
     * @return AbstractFile
     * @throws InvalidOptionsException
     * @throws LogicException
     * @throws ValidationException
     */
    public function __invoke(Request $request): AbstractFile
    {
        $mediaObject = $this::getFileNewInstance();
        // POZOR: form factory potřebuje třídu FORMULÁŘE (FormType), NE entity.
        // getFileClassName() vrací entitu (správně pro AbstractFileType::data_class),
        // proto samostatná getFileFormClass() vrací odpovídající *Type. Dřív se sem
        // omylem dávala getFileClassName() (entita) → „does not implement FormTypeInterface" 500.
        $form = $this->factory->create($this::getFileFormClass(), $mediaObject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($mediaObject);
            $em->flush();
            $mediaObject->setFile(null); // Prevent the serialization of the file property.

            return $mediaObject;
        }
        throw new ValidationException($this->validator->validate($mediaObject)); // This will be handled by API Platform and returns a validation error.
    }

    abstract public static function getFileNewInstance(): AbstractFile;

    /** Třída ENTITY (media objektu) — používá {@see AbstractFileType} pro `data_class`. */
    abstract public static function getFileClassName(): string;

    /** Třída FORMULÁŘE (FormType) pro nahrání — používá form factory v {@see __invoke}. */
    abstract public static function getFileFormClass(): string;
}
