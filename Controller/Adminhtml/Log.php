<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Beehexa\HexaSync\Controller\Adminhtml;

abstract class Log extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    public const ADMIN_RESOURCE = 'Beehexa_HexaSync::log_view';

    public const MENU_ID = 'Beehexa_HexaSync::beehexa_hexasync_log';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::MENU_ID)
            ->addBreadcrumb(__('Beehexa'), __('Beehexa'))
            ->addBreadcrumb(__('Log'), __('Log'));
        return $resultPage;
    }
}

