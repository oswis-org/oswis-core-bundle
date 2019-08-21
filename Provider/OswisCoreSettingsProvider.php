<?php

namespace Zakjakub\OswisCoreBundle\Provider;

/**
 * Provider of settings for core module of OSWIS.
 */
class OswisCoreSettingsProvider
{

    /**
     * @var array
     */
    protected $app;

    /**
     * @var array
     */
    protected $admin;

    /**
     * @var array
     */
    protected $email;

    /**
     * @var array
     */
    protected $web;

    /**
     * OswisCoreSettingsProvider constructor.
     *
     * @param array|null $app
     * @param array|null $admin
     * @param array|null $email
     * @param array|null $web
     */
    public function __construct(
        ?array $app,
        ?array $admin,
        ?array $email,
        ?array $web
    ) {
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

    /**
     * @return array
     */
    final public function getApp(): array
    {
        return $this->app;
    }

    /**
     * @return array
     */
    final public function getAdmin(): array
    {
        return $this->admin;
    }

    /**
     * @return array
     */
    final public function getEmail(): array
    {
        return $this->email;
    }

    /**
     * @return array
     */
    final public function getWeb(): array
    {
        return $this->web;
    }

    /**
     * @return string
     */
    final public function getCoreAppName(): string
    {
        return 'OSWIS';
    }

    /**
     * @return string
     */
    final public function getCoreAppNameShort(): string
    {
        return 'OSWIS';
    }

    /**
     * @return string
     */
    final public function getCoreAppNameLong(): string
    {
        return 'One Simple Web IS';
    }
}
