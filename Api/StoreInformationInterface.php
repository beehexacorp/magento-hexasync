<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

use \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

interface StoreInformationInterface
{
    /**
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface[]
     */
    public function getList();

    /**
     * @param string $storeId
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function get(string $storeId);
}
