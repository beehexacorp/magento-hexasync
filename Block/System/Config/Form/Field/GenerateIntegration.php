<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\System\Config\Form\Field;

use Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class GenerateIntegration extends Field
{
    /**
     * @var HexaSyncIntegrationManagement
     */
    protected HexaSyncIntegrationManagement $integrationManager;

    /**
     * @param Context $context
     * @param HexaSyncIntegrationManagement $integrationManager
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context                       $context,
        HexaSyncIntegrationManagement $integrationManager,
        array                         $data = [],
        ?SecureHtmlRenderer           $secureRenderer = null
    ) {
        parent::__construct($context, $data, $secureRenderer);
        $this->integrationManager = $integrationManager;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout(): \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
    {
        parent::_prepareLayout();
        $this->setTemplate('Beehexa_HexaSync::system/config/generate_integration.phtml');
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $integration = $this->integrationManager->getIntegration();
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('hexasync/integration/regenerate'),
                'disabled' => !!$integration->getId()
            ]
        );
        return $this->_toHtml();
    }
}
