<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ScopeInterface;

class Data extends AbstractHelper
{
    public const XML_CONFIG_PREFIX                = 'beehexa';
    public const XML_CONFIG_SERVICE_ENDPOINT_PATH = 'beehexa/hexasync/service_endpoint';

    /**
     * Getting service endpoint.
     *
     * @param string  $scopeType
     * @param ?string $scopeCode
     * @return mixed
     */
    public function getServiceEndpoint($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_SERVICE_ENDPOINT_PATH, $scopeType, $scopeCode);
    }

    /**
     * Get config value
     *
     * @param string  $path
     * @param string  $scopeType
     * @param ?string $scopeCode
     * @return ?string
     */
    public function getConfigValue(
        string  $path,
        string  $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        ?string $scopeCode = null
    ): ?string {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PREFIX . '/' . $path, $scopeType, $scopeCode);
    }

    /**
     * Get config flag
     *
     * @param string  $path
     * @param string  $scopeType
     * @param ?string $scopeCode
     * @return bool
     */
    public function getConfigFlag(
        string  $path,
        string  $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_PREFIX . '/' . $path, $scopeType, $scopeCode);
    }
}
