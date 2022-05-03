<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;

interface HexaSyncIntegrationInterface
{
    /**
     * @param string $name
     * @return HexaSyncIntegrationDataInterface
     */
    public function getByName(string $name): HexaSyncIntegrationDataInterface;

    /**
     * @param HexaSyncInfoDataInterface $connector
     * @return HexaSyncInfoDataInterface
     */
    public function saveConnectorInfo(HexaSyncInfoDataInterface $connector): HexaSyncInfoDataInterface;

    /**
     * @return int
     */
    public function generateIntegration(): int;
}
