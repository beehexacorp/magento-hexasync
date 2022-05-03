<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;
use Magento\Integration\Model\Integration as IntegrationModel;

interface HexaSyncIntegrationInterface
{
    /**
     * @param $name
     * @return HexaSyncIntegrationDataInterface
     */
    public function getByName($name);

    /**
     * @param string|null $storeCode
     * @return HexaSyncIntegrationDataInterface
     */
    public function register(?string $storeCode);

    /**
     * @return int
     */
    public function generate();
}
