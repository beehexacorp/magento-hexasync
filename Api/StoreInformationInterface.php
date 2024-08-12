<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

use Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

interface StoreInformationInterface
{
    /**
     * @return StoreInformationDataInterface[]
     */
    public function getList(): array;

    /**
     * @param string $storeId
     * @return StoreInformationDataInterface
     */
    public function get(string $storeId): \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;
}
