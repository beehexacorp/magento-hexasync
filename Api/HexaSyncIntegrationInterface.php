<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;

interface HexaSyncIntegrationInterface
{
    /**
     * @param string $name
     * @return \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface
     */
    public function getByName(string $name): \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;

    /**
     * @param \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface $connector
     * @return \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface
     */
    public function saveConnectorInfo(\Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface $connector): \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;

    /**
     * @return int
     */
    public function generateIntegration(): int;
}
