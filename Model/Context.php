<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Helper\Data as BeehexaData;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory;

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


    public function __construct(
        BeehexaData                             $beehexaHelper,
        HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory
    )
    {
        $this->beehexaHelper = $beehexaHelper;
        $this->hexaSyncIntegrationDataInterfaceFactory = $hexaSyncIntegrationDataInterfaceFactory;
    }

    /**
     * @return HexaSyncIntegrationDataInterfaceFactory
     */
    public function getHexaSyncIntegrationDataInterfaceFactory(): HexaSyncIntegrationDataInterfaceFactory
    {
        return $this->hexaSyncIntegrationDataInterfaceFactory;
    }

    /**
     * @return BeehexaData
     */
    public function getBeehexaHelper(): BeehexaData
    {
        return $this->beehexaHelper;
    }

}
