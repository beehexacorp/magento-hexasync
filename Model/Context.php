<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory;
use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterfaceFactory;
use Beehexa\HexaSync\Helper\Data as BeehexaData;
use Beehexa\HexaSync\Helper\RegisterInformation;

class Context
{
    /**
     * @var HexaSyncIntegrationDataInterfaceFactory
     */
    protected $hexaSyncIntegrationDataInterfaceFactory;

    /**
     * @var BeehexaData
     */
    protected $beehexaHelper;

    /**
     * @var RegisterInformation
     */
    protected $registerInformation;

    /**
     * @var HexaSyncInfoDataInterfaceFactory
     */
    protected $hexaSyncInfoDataInterfaceFactory;

    /**
     * @param BeehexaData                             $beehexaHelper
     * @param RegisterInformation                             $registerInformation
     * @param HexaSyncInfoDataInterfaceFactory        $hexaSyncInfoDataInterfaceFactory
     * @param HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory
     */
    public function __construct(
        BeehexaData                             $beehexaHelper,
        RegisterInformation                             $registerInformation,
        HexaSyncInfoDataInterfaceFactory        $hexaSyncInfoDataInterfaceFactory,
        HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory
    ) {
        $this->beehexaHelper = $beehexaHelper;
        $this->hexaSyncInfoDataInterfaceFactory = $hexaSyncInfoDataInterfaceFactory;
        $this->registerInformation = $registerInformation;
        $this->hexaSyncIntegrationDataInterfaceFactory = $hexaSyncIntegrationDataInterfaceFactory;
    }

    /**
     * Getter for HexaSyncIntegrationDataInterfaceFactory
     *
     * @return \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory
     */
    public function getHexaSyncIntegrationDataInterfaceFactory(): \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory
    {
        return $this->hexaSyncIntegrationDataInterfaceFactory;
    }

    /**
     * Getter for HexaSyncInfoDataInterfaceFactory
     *
     * @return \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterfaceFactory
     */
    public function getHexaSyncInfoDataInterfaceFactory(): \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterfaceFactory
    {
        return $this->hexaSyncInfoDataInterfaceFactory;
    }

    /**
     * Getter for BeehexaData
     *
     * @return BeehexaData
     */
    public function getBeehexaHelper(): BeehexaData
    {
        return $this->beehexaHelper;
    }

    /**
     * Getter for RegisterInformation
     *
     * @return RegisterInformation
     */
    public function getRegisterHelper(): RegisterInformation
    {
        return $this->registerInformation;
    }
}
