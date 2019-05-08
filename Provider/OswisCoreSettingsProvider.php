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
     * OswisCoreSettingsProvider constructor.
     *
     * @param array|null $app
     * @param array|null $admin
     * @param array|null $email
     */
    public function __construct(
        ?array $app,
        ?array $admin,
        ?array $email
    ) {
        $this->app = $app;
        $this->admin = $admin;
        $this->email = $email;
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