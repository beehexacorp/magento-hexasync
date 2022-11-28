<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\System\Config\Form\Field;

use \Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class RegisteredInformation extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var HexaSyncIntegrationManagement
     */
    protected $hexaSyncIntegrationManagement;

    public function __construct(
        Context                       $context,
        HexaSyncIntegrationManagement $hexaSyncIntegrationManagement,
        array                         $data = [],
        ?SecureHtmlRenderer           $secureRenderer = null
    ) {
        parent::__construct($context, $data, $secureRenderer);
        $this->hexaSyncIntegrationManagement = $hexaSyncIntegrationManagement;
    }

    protected function getCurrentStore(){
        $storeId = $this->getRequest()->getParam('store', true);
        $store = $this->_storeManager->getStore($storeId);
        return $store;
    }

    public function _getElementHtml($element)
    {
        $registeredInfo = $this->hexaSyncIntegrationManagement->getConnectorInfo($this->getCurrentStore()->getId());
        $html = sprintf("<div><strong>Current Store: </strong>%s</div>",  $this->getCurrentStore()->getName());
        if($registeredInfo->getAccount()) {
            $html .= sprintf("<div><span><strong>Account:</strong> %s</span></div>", $registeredInfo->getAccount());
            $html .= sprintf("<div><span><strong>Registered Store Name:</strong> %s</span></div>", $registeredInfo->getStoreName());
            $html .= sprintf("<div><span><strong>Hexasync Version:</strong> %s</span></div>", $registeredInfo->getVersion());
            $html .= sprintf("<div><span><strong>Status:</strong> %s</span></div>", $registeredInfo->getStatus() ? "Active" : "Suspended");
        } else {
            $html .= "<div><span style='color: darkred'><strong>Connect your store</strong></span></div>";
        }
        return $html;
    }
}
