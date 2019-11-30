<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Utils;

use Psr\Log\LoggerInterface;

class SpamDateTimeEncoder
{
    private string $secret_key = 'fe67d68ee1e09b47acd8810b880d537034c10c15344433a992b9c79002666844';

    final public function encrypt(string $string): string
    {
        return base64_encode(openssl_encrypt($string, 'AES-256-CBC', hash('sha256', $this->secret_key)));
    }

    final public function isSpam(string $string, LoggerInterface $logger): bool
    {
        $time_till_unlock = 7;
        if ($this->decrypt($string) > strtotime('-'.$time_till_unlock.' seconds')) {
            $logger->notice(
                'TIME SPAM: '.$string.', '.strtotime('-'.$time_till_unlock.' seconds')
            );

            return true;
        }

        return false;
    }

    final public function decrypt(string $string): string
    {
        return openssl_decrypt(base64_decode($string, true), 'AES-256-CBC', hash('sha256', $this->secret_key));
    }
}
