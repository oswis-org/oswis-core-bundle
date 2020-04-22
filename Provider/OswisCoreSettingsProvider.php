<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Provider;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Exception\RfcComplianceException;

/**
 * Provider of settings for core module of OSWIS.
 */
class OswisCoreSettingsProvider
{
    protected ?array $app = null;

    protected ?array $admin = null;

    protected ?array $email = null;

    protected ?array $web = null;

    protected ?array $adminIPs = null;

    public function __construct(?array $app, ?array $admin, ?array $email, ?array $web, ?array $adminIPs)
    {
        $this->app = $app;
        $this->admin = $admin;
        $this->email = $email;
        $this->web = $web;
        $this->adminIPs = $adminIPs;
    }

    final public function getArray(): array
    {
        return [
            'app'       => $this->getApp(),
            'admin'     => $this->getAdmin(),
            'email'     => $this->getEmail(),
            'web'       => $this->getWeb(),
            'admin_ips' => $this->getAdminIPs(),
        ];
    }

    final public function getApp(): array
    {
        return $this->app;
    }

    final public function getAdmin(): array
    {
        return $this->admin;
    }

    final public function getEmail(): array
    {
        return $this->email;
    }

    final public function getWeb(): array
    {
        return $this->web;
    }

    final public function getAdminIPs(): array
    {
        return $this->adminIPs;
    }

    final public function getCoreAppName(): string
    {
        return 'OSWIS';
    }

    final public function getCoreAppNameShort(): string
    {
        return 'OSWIS';
    }

    final public function getCoreAppNameLong(): string
    {
        return 'One Simple Web IS';
    }

    /**
     * @return Address
     * @throws LogicException
     * @throws RfcComplianceException
     */
    public function getArchiveMailerAddress(): Address
    {
        return new Address($mailSettings['archive_address'] ?? '', $mailSettings['archive_name'] ?? '');
    }
}
