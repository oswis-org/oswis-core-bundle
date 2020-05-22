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
 * Provider of settings for OSWIS core module.
 */
class OswisCoreSettingsProvider
{
    protected array $app = [];

    protected array $admin = [];

    protected array $email = [];

    protected array $web = [];

    protected array $adminIPs = [];

    protected array $angularAdmin = [];

    public function __construct(array $app, array $admin, array $email, array $web, array $adminIPs, array $angularAdmin)
    {
        $this->app = $app;
        $this->admin = $admin;
        $this->email = $email;
        $this->web = $web;
        $this->adminIPs = $adminIPs;
        $this->angularAdmin = $angularAdmin;
    }

    final public function getArray(): array
    {
        return [
            'app'           => $this->getApp(),
            'admin'         => $this->getAdmin(),
            'email'         => $this->getEmail(),
            'web'           => $this->getWeb(),
            'admin_ips'     => $this->getAdminIPs(),
            'angular_admin' => $this->getAngularAdmin(),
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

    final public function getAngularAdmin(): array
    {
        return $this->angularAdmin;
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
        return new Address($this->getEmail()['archive_address'] ?? '', $this->getEmail()['archive_name'] ?? '');
    }
}
