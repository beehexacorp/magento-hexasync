<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\System\Config\Form\Field;

use Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Store\Api\Data\StoreInterface;

class RegisteredInformation extends Field
{

    /**
     *
     * @var HexaSyncIntegrationManagement
     */
    protected HexaSyncIntegrationManagement $hexaSyncIntegrationManagement;

    /**
     *
     * RegisteredInformation constructor
     *
     * @param Context $context
     * @param HexaSyncIntegrationManagement $hexaSyncIntegrationManagement
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
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
    public function _getElementHtml($element): string
    {
        $store = $this->getCurrentStore();
        $registerInfo = $this->hexaSyncIntegrationManagement->getConnectorInfo((string)$store->getId());
        $html = $this->renderRow(__('Current Store:'), (string)$store->getName());
        if ($registerInfo->getAccount()) {
            $status = $registerInfo->getStatus() ? __('Active') : __('Suspended');
            $html .= $this->renderRow(__('Account:'), (string)$registerInfo->getAccount());
            $html .= $this->renderRow(__('Registered Store Name:'), (string)$registerInfo->getStoreName());
            $html .= $this->renderRow(__('Hexasync Version:'), (string)$registerInfo->getVersion());
            $html .= $this->renderRow(__('Status:'), (string)$status);
        } else {
            $html .= sprintf(
                '<div><span style="color: darkred"><strong>%s</strong></span></div>',
                $this->_escaper->escapeHtml(__('Connect your store'))
            );
        }
        return $html;
    }

    /**
     * Render one "label: value" line with both parts escaped
     *
     * @param \Magento\Framework\Phrase|string $label
     * @param string $value
     * @return string
     */
    private function renderRow($label, string $value): string
    {
        return sprintf(
            '<div><strong>%s</strong> %s</div>',
            $this->_escaper->escapeHtml($label),
            $this->_escaper->escapeHtml($value)
        );
    }

    /**
     * Getting current store.
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    protected function getCurrentStore(): StoreInterface
    {
        $storeId = $this->getRequest()->getParam('store', true);
        $store = $this->_storeManager->getStore($storeId);
        return $store;
    }
}
