<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Controller\Adminhtml\Integration;

use Beehexa\HexaSync\Api\HexaSyncIntegrationInterface;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\GroupRepository;

class Register extends BackendAction
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Magento_Integration::integrations';

    /**
     * @var HexaSyncIntegrationInterface
     */
    protected $hexaSyncManagement;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @param Context                      $context
     * @param StoreManagerInterface        $storeManager
     * @param GroupRepository              $groupRepository
     * @param HexaSyncIntegrationInterface $hexaSyncManagement
     */
    public function __construct(
        Context                      $context,
        StoreManagerInterface        $storeManager,
        GroupRepository              $groupRepository,
        HexaSyncIntegrationInterface $hexaSyncManagement
    ) {
        parent::__construct($context);
        $this->hexaSyncManagement = $hexaSyncManagement;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Register store with Hexasync
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = [
            'success'      => false,
            'errorMessage' => '',
        ];
        /** @var \Magento\Framework\App\RequestInterface $request */
        $websiteId = (int)$this->_request->getParam('website', 0);
        $storeId = (int)$this->_request->getParam('store', 0);
        if (!$storeId) {
            if ($websiteId) {
                $website = $this->storeManager->getWebsite($websiteId);
                $group = $this->groupRepository->get($website->getDefaultGroupId());
                $storeId = $group->getDefaultStoreId();
            }
        }
        // @TODO implement register
        $this->hexaSyncManagement->register($storeId);
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
