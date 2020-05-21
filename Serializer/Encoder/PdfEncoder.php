<?php

namespace OswisOrg\OswisCoreBundle\Serializer\Encoder;

use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class PdfEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT = 'pdf';

    /**
     * {@inheritDoc}
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function encode($data, string $format, array $context = []): string
    {
        if ($data instanceof Collection) {
            return 'IS_Collection';
        }
        if (is_array($data)) {
            return 'IS_Array';
        }
        if ($data instanceof BasicInterface) {
            return 'IS_Basic';
        }

        return 'IS_Nevim';
    }

    final public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

    final public function supportsDecoding(string $format): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @noinspection MissingReturnTypeInspection
     */
    final public function decode(string $data, string $format, array $context = [])
    {
        return null;
    }
}
