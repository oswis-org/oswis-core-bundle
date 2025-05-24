<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\AbstractClass;

use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractImage;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

abstract class AbstractImageAction extends AbstractFileAction
{
    abstract public static function getFileNewInstance(): AbstractImage;

    /**
     * @param  Request  $request
     *
     * @return AbstractImage
     * @throws InvalidOptionsException
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    public function __invoke(Request $request): AbstractImage
    {
        $result = parent::__invoke($request);
        if (!($result instanceof AbstractImage)) {
            throw new InvalidArgumentException('Image must be instance of AbstractImage.');
        }

        return $result;
    }
}
