<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory;
use Beehexa\HexaSync\Helper\Data as BeehexaData;

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
     * @param BeehexaData                             $beehexaHelper
     * @param HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory
     */
    public function __construct(
        BeehexaData                             $beehexaHelper,
        HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory
    ) {
        $this->beehexaHelper = $beehexaHelper;
        $this->hexaSyncIntegrationDataInterfaceFactory = $hexaSyncIntegrationDataInterfaceFactory;
    }

    /**
     * Getter for HexaSyncIntegrationDataInterfaceFactory
     *
     * @return HexaSyncIntegrationDataInterfaceFactory
     */
    public function getHexaSyncIntegrationDataInterfaceFactory(): HexaSyncIntegrationDataInterfaceFactory
    {
        return $this->hexaSyncIntegrationDataInterfaceFactory;
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
}
