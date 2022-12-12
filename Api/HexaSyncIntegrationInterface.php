<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

interface HexaSyncIntegrationInterface
{
    /**
     * Get Connector by Name
     *
     * @param string $name
     * @return \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface
     */
    public function getByName(string $name): \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;

    /**
     * Saving connector info
     *
     * @param \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface $connector
     * @return \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface
     */
    public function saveConnectorInfo(\Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface $connector):
            \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;

    /**
     * Getting Connector information
     *
     * @param ?string $storeId
     * @return Data\HexaSyncInfoDataInterface
     */
    public function getConnectorInfo(string $storeId = null): \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;

    /**
     * Encrypting data
     *
     * @param \Magento\Framework\DataObject $hexaSyncData
     * @return string
     */
    public function encrypt(\Magento\Framework\DataObject $hexaSyncData);

    /**
     * Generate integration
     *
     * @return \Magento\Integration\Model\Integration
     */
    public function generateIntegration();

    /**
     * Activate predefined integration user
     *
     * @param \Magento\Integration\Model\Integration $integration
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function activateIntegration($integration = null);
}
