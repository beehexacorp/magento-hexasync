<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class RegisterInformation extends Data
{
    /**
     * Getting account from config
     *
     * @param string $scopeType
     * @param ?string $scopeCode
     * @return ?string
     */
    public function getAccount($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): ?string
    {
        return $this->getConfigValue('connector/account', $scopeType, $scopeCode);
    }

    /**
     * Getting status from config
     *
     * @param string $scopeType
     * @param ?string $scopeCode
     * @return ?string
     */
    public function getStatus($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): ?string
    {
        return $this->getConfigValue('connector/status', $scopeType, $scopeCode);
    }

    /**
     * Getting store name from config
     *
     * @param string $scopeType
     * @param ?string $scopeCode
     * @return ?string
     */
    public function getStoreName($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): ?string
    {
        return $this->getConfigValue('connector/store_name', $scopeType, $scopeCode);
    }

    /**
     * Getting API version from config
     *
     * @param string $scopeType
     * @param ?string $scopeCode
     * @return ?string
     */
    public function getAPIVersion($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): ?string
    {
        return $this->getConfigValue('connector/version', $scopeType, $scopeCode);
    }
}
