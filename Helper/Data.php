<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public const XML_CONFIG_SERVICE_ENDPOINT_PATH = 'beehexa/hexasync/service_endpoint';

    public function getServiceEndpoint($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null){
        return $this->scopeConfig->getValue(self::XML_CONFIG_SERVICE_ENDPOINT_PATH, $scopeType, $scopeCode);
    }
}
