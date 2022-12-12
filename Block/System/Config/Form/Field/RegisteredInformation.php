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
     *
     * @var HexaSyncIntegrationManagement
     */
    protected $hexaSyncIntegrationManagement;

    /**
     *
     * RegisteredInformation constructor
     *
     * @param Context                       $context
     * @param HexaSyncIntegrationManagement $hexaSyncIntegrationManagement
     * @param array                         $data
     * @param SecureHtmlRenderer|null       $secureRenderer
     */
    public function __construct(
        Context                       $context,
        HexaSyncIntegrationManagement $hexaSyncIntegrationManagement,
        array                         $data = [],
        ?SecureHtmlRenderer           $secureRenderer = null
    ) {
        parent::__construct($context, $data, $secureRenderer);
        $this->hexaSyncIntegrationManagement = $hexaSyncIntegrationManagement;
    }

    /**
     * @inheritDoc
     */
    public function _getElementHtml($element)
    {
        $registerInfo = $this->hexaSyncIntegrationManagement->getConnectorInfo($this->getCurrentStore()->getId());
        $html = sprintf("<div><strong>Current Store: </strong>%s</div>", $this->getCurrentStore()->getName());
        if ($registerInfo->getAccount()) {
            $status = $registerInfo->getStatus() ? "Active" : "Suspended";
            $html .= sprintf("<div><strong>Account:</strong> %s</div>", $registerInfo->getAccount());
            $html .= sprintf("<div><strong>Registered Store Name:</strong> %s</div>", $registerInfo->getStoreName());
            $html .= sprintf("<div><strong>Hexasync Version:</strong> %s</div>", $registerInfo->getVersion());
            $html .= sprintf("<div><strong>Status:</strong> %s</div>", $status);
        } else {
            $html .= "<div><span style='color: darkred'><strong>Connect your store</strong></span></div>";
        }
        return $html;
    }

    /**
     * Getting current store.
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCurrentStore()
    {
        $storeId = $this->getRequest()->getParam('store', true);
        $store = $this->_storeManager->getStore($storeId);
        return $store;
    }
}
