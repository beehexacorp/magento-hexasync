<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

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
     * @param Data\HexaSyncInfoDataInterface $hexaSyncData
     * @return string
     */
    public function encrypt(\Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface $hexaSyncData);

    /**
     * @return \Magento\Integration\Model\Integration
     */
    public function generateIntegration();

    /**
     * Activate predefined integration user
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function activateIntegration($integration = null);
}
