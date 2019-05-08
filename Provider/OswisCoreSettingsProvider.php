<?php


namespace Zakjakub\OswisCoreBundle\Provider;

/**
 * Provider of settings for core module of OSWIS.
 */
class OswisCoreSettingsProvider
{

    /**
     * @var int
     */
    protected $dummyParameterInteger;

    /**
     * @var boolean
     */
    protected $dummyParameterBoolean;

    /**
     * @var string
     */
    protected $coreAppName = 'OSWIS';

    /**
     * @var string
     */
    protected $coreAppNameShort = 'OSWIS';

    /**
     * @var string
     */
    protected $coreAppNameLong = 'One Simple Web IS';

    /**
     * @var string
     */
    protected $emailSenderName = 'One Simple Web IS';

    /**
     * @var string
     */
    protected $emailSenderAddress = 'oswis@oswis.org';

    /**
     * OswisCoreSettingsProvider constructor.
     *
     * @param int|null  $dummyParameterInteger
     * @param bool|null $dummyParameterBoolean
     */
    public function __construct(
        ?int $dummyParameterInteger,
        ?bool $dummyParameterBoolean
    ) {
        $this->dummyParameterInteger = $dummyParameterInteger;
        $this->dummyParameterBoolean = $dummyParameterBoolean;
    }

    /**
     * @return int
     */
    final public function getDummyParameterInteger(): int
    {
        return $this->dummyParameterInteger;
    }

    /**
     * @return bool
     */
    final public function isDummyParameterBoolean(): bool
    {
        return $this->dummyParameterBoolean;
    }

    /**
     * @return string
     */
    final public function getCoreAppName(): string
    {
        return $this->coreAppName;
    }

    /**
     * @return string
     */
    final public function getCoreAppNameShort(): string
    {
        return $this->coreAppNameShort;
    }

    /**
     * @return string
     */
    final public function getCoreAppNameLong(): string
    {
        return $this->coreAppNameLong;
    }


}