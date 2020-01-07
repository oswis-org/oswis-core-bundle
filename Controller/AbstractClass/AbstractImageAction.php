<?php

namespace Zakjakub\OswisCoreBundle\Controller\AbstractClass;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Doctrine\Common\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zakjakub\OswisCoreBundle\Entity\AbstractClass\AbstractImage;

abstract class AbstractImageAction
{
    private ValidatorInterface $validator;

    private ManagerRegistry $doctrine;

    private FormFactoryInterface $factory;

    public function __construct(ManagerRegistry $doctrine, FormFactoryInterface $factory, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->doctrine = $doctrine;
        $this->factory = $factory;
    }

    abstract public static function getImageNewInstance(): AbstractImage;

    abstract public static function getImageClassName(): string;

    /**
     * @param Request $request
     *
     * @return AbstractImage
     * @throws InvalidOptionsException
     * @throws LogicException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    final public function __invoke(Request $request): AbstractImage
    {
        $mediaObject = static::getImageNewInstance();
        $form = $this->factory->create($this::getImageClassName(), $mediaObject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($mediaObject);
            $em->flush();
            $mediaObject->file = null; // Prevent the serialization of the file property.

            return $mediaObject;
        }
        throw new ValidationException($this->validator->validate($mediaObject)); // This will be handled by API Platform and returns a validation error.
    }
}
