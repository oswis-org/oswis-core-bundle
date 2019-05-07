<?php


namespace Zakjakub\OswisCoreBundle\Provider;


/**
 * Class OswisCoreSettingsProvider
 * @package Zakjakub\OswisCoreBundle\Provider
 */
class OswisCoreSettingsProvider
{

    /**
     * @var int
     */
    public $dummyParameterInteger;

    /**
     * @var boolean
     */
    public $dummyParameterBoolean;

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

}