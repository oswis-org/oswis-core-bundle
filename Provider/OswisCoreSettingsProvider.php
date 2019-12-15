<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Provider;

/**
 * Provider of settings for core module of OSWIS.
 */
class OswisCoreSettingsProvider
{
    protected ?array $app = null;

    protected ?array $admin = null;

    protected ?array $email = null;

    protected ?array $web = null;

    public function __construct(?array $app, ?array $admin, ?array $email, ?array $web)
    {
        $this->app = $app;
        $this->admin = $admin;
        $this->email = $email;
        $this->web = $web;
    }

    final public function getArray(): array
    {
        return [
            'app'   => $this->getApp(),
            'admin' => $this->getAdmin(),
            'email' => $this->getEmail(),
            'web'   => $this->getWeb(),
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
}
