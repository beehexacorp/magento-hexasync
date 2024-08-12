<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api;

use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Model\Integration;

interface HexaSyncIntegrationInterface
{
    /**
     * Get Connector by Name
     *
     * @param string $name
     * @return HexaSyncIntegrationDataInterface
     */
    public function getByName(string $name): \Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;

    /**
     * Saving connector info
     *
     * @param HexaSyncInfoDataInterface $connector
     * @return HexaSyncInfoDataInterface
     */
    public function saveConnectorInfo(\Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface $connector): \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;

    /**
     * Getting Connector information
     *
     * @param ?string $storeId
     * @return HexaSyncInfoDataInterface
     */
    public function getConnectorInfo(string $storeId = null): \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;

    /**
     * Encrypting data
     *
     * @param DataObject $hexaSyncData
     * @return string
     */
    public function encrypt(DataObject $hexaSyncData): string;

    /**
     * Generate integration
     *
     * @return Integration
     */
    public function generateIntegration(): \Magento\Integration\Model\Integration;

    /**
     * Activate predefined integration user
     *
     * @param Integration|null $integration
     * @return bool
     * @throws NoSuchEntityException
     * @throws IntegrationException
     */
    public function activateIntegration(?Integration $integration = null): bool;
}
