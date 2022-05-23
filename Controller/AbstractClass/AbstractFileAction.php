<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\AbstractClass;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Doctrine\Persistence\ManagerRegistry;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractFile;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractFileAction
{
    public function __construct(protected ManagerRegistry $doctrine, protected FormFactoryInterface $factory, protected ValidatorInterface $validator)
    {
    }

    abstract public static function getFileNewInstance(): AbstractFile;

    abstract public static function getFileClassName(): string;

    /**
     * @param  Request  $request
     *
     * @return AbstractFile
     * @throws \ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function __invoke(Request $request): AbstractFile
    {
        $mediaObject = $this::getFileNewInstance();
        $form = $this->factory->create($this::getFileClassName(), $mediaObject);
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
}
