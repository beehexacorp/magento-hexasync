<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use \Beehexa\HexaSync\Helper\Data as HexaSyncData;
use \Magento\Store\Model\ScopeInterface;

class RegisterStore extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_hexasyncHelper;

    public function __construct(
        Context             $context,
        HexaSyncData        $hexasyncHelper,
        array               $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->_hexasyncHelper = $hexasyncHelper;
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * Set template to itself
     *
     * @return $this
     * @since 100.1.0
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Beehexa_HexaSync::system/config/register_store.phtml');
        return $this;
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @since 100.1.0
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id'      => $element->getHtmlId(),
                'hexasync_url' => $this->_hexasyncHelper->getServiceEndpoint(ScopeInterface::SCOPE_STORE),
                'ajax_url'     => $this->_urlBuilder->getUrl(
                    'hexasync/integration/register',
                    ['_use_rewrite' => true, '_current' => true]
                )
            ]
        );

        return $this->_toHtml();
    }
}
