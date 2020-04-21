<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Controller\AbstractClass;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractImage;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

abstract class AbstractImageAction extends AbstractFileAction
{
    abstract public static function getFileNewInstance(): AbstractImage;

    /**
     * @param Request $request
     *
     * @return AbstractImage
     * @throws InvalidOptionsException
     * @throws LogicException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function __invoke(Request $request): AbstractImage
    {
        $result = parent::__invoke($request);
        assert($result instanceof AbstractImage);

        return $result;
    }
}
