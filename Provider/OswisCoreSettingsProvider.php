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
    protected $emailSenderName;

    /**
     * @var string
     */
    protected $emailSenderAddress;

    /**
     * OswisCoreSettingsProvider constructor.
     *
     * @param int|null    $dummyParameterInteger
     * @param bool|null   $dummyParameterBoolean
     * @param string|null $emailSenderAddress
     * @param string|null $emailSenderName
     */
    public function __construct(
        ?int $dummyParameterInteger,
        ?bool $dummyParameterBoolean,
        ?string $emailSenderAddress,
        ?string $emailSenderName
    ) {
        $this->dummyParameterInteger = $dummyParameterInteger;
        $this->dummyParameterBoolean = $dummyParameterBoolean;
        $this->emailSenderAddress = $emailSenderAddress;
        $this->emailSenderName = $emailSenderName;
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