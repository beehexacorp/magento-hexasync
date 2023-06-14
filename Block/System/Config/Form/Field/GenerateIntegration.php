<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;

class GenerateIntegration extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var HexaSyncIntegrationManagement
     */
    protected $integrationManager;

    /**
     * @param Context                       $context
     * @param HexaSyncIntegrationManagement $integrationManager
     * @param array                         $data
     * @param SecureHtmlRenderer|null       $secureRenderer
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
     * Internal constructor, that is called from real constructor
     *
     * @return void
     */
    protected function _construct()
    {
        /**
         * Please override this one instead of overriding real __construct constructor
         */
        parent::_construct();

        if ($this->hasData('template')) {
            $this->setData('template', 'Beehexa_HexaSync::system/config/generate_integration.phtml');
        }
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $integration = $this->integrationManager->getIntegration();
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id'      => $element->getHtmlId(),
                'ajax_url'     => $this->_urlBuilder->getUrl('hexasync/integration/regenerate'),
                'disabled'     => !!$integration->getId()
            ]
        );
        return $this->_toHtml();
    }
}
