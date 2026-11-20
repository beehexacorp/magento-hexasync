<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\System\Config\Form\Field;

use Beehexa\HexaSync\Helper\Data as HexaSyncData;
use Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;
use Beehexa\HexaSync\Model\OauthHandshake;
use Magento\Integration\Model\Integration;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Store\Model\ScopeInterface;

class RegisterStore extends Field
{
    /**
     * @var HexaSyncData
     */
    protected $_hexasyncHelper;

    /**
     * @var OauthHandshake
     */
    private OauthHandshake $oauthHandshake;

    /**
     * @var HexaSyncIntegrationManagement
     */
    private HexaSyncIntegrationManagement $integrationManagement;

    /**
     * @param Context $context
     * @param HexaSyncData $hexasyncHelper
     * @param OauthHandshake $oauthHandshake
     * @param HexaSyncIntegrationManagement $integrationManagement
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context                       $context,
        HexaSyncData                  $hexasyncHelper,
        OauthHandshake                $oauthHandshake,
        HexaSyncIntegrationManagement $integrationManagement,
        array                         $data = [],
        ?SecureHtmlRenderer           $secureRenderer = null
    ) {
        $this->_hexasyncHelper = $hexasyncHelper;
        $this->oauthHandshake = $oauthHandshake;
        $this->integrationManagement = $integrationManagement;
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * Set template to itself
     *
     * @return $this
     * @since 100.1.0
     */
    protected function _prepareLayout(): static
    {
        parent::_prepareLayout();
        $this->setTemplate('Beehexa_HexaSync::system/config/register_store.phtml');
        return $this;
    }

    /**
     * Service endpoint saved for the scope currently being edited
     *
     * @return string|null
     */
    private function getScopedServiceEndpoint(): ?string
    {
        $store = $this->getRequest()->getParam('store');
        $website = $this->getRequest()->getParam('website');
        if ($store) {
            return $this->_hexasyncHelper->getServiceEndpoint(ScopeInterface::SCOPE_STORE, (string)$store);
        }
        if ($website) {
            return $this->_hexasyncHelper->getServiceEndpoint(ScopeInterface::SCOPE_WEBSITE, (string)$website);
        }
        return $this->_hexasyncHelper->getServiceEndpoint();
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     * @since 100.1.0
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'hexasync_url' => $this->getScopedServiceEndpoint(),
                'allow_insecure' => $this->oauthHandshake->isInsecureEndpointAllowed(),
                'already_active' => $this->integrationManagement->getIntegration()->getStatus()
                    == Integration::STATUS_ACTIVE,
                'ajax_url' => $this->_urlBuilder->getUrl(
                    'hexasync/integration/register',
                    ['_use_rewrite' => true, '_current' => true]
                )
            ]
        );

        return $this->_toHtml();
    }
}
