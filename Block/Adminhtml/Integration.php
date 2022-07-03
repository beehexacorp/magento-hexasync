<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Block\Adminhtml;

use Magento\Integration\Block\Adminhtml\Integration as DefaultIntegration;
use Magento\Backend\Block\Widget\Context;
use Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;

class Integration extends DefaultIntegration
{
    /**
     * @var HexaSyncIntegrationManagement
     */
    protected $hexaSyncManagement;

    /**
     * @param Context                            $context
     * @param array                              $data
     * @param HexaSyncIntegrationManagement|null $hexaSyncManagement
     */
    public function __construct(
        Context                       $context,
        array                         $data = [],
        ?HexaSyncIntegrationManagement $hexaSyncManagement = null
    ) {
        $this->hexaSyncManagement = $hexaSyncManagement;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();

        if (!$this->hexaSyncManagement->getIntegration()->getId()) {
            $message = __('Generate HexaSync Integration User?');
            $this->buttonList->add(
                'generate_hexasync_integration',
                [
                    'label'   => __('Generate HexaSync User'),
                    'onclick' => 'confirmSetLocation(\'' . $message . '\', \'' . $this->getGenerateUrl() . '\', {data: {regenerate: true}})',
                    'class'   => 'generate-hexasync-integration'
                ]
            );
        }
    }

    /**
     * Getter for generate url
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('hexasync/integration/regenerate');
    }
}
