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
use Magento\Integration\Model\Integration;
use Magento\Store\Model\GroupRepository;
use Magento\Store\Model\StoreManagerInterface;

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
        try {
            $integration = $this->hexaSyncManagement->getIntegration();
            if (!$integration->getId() || $integration->getStatus() == Integration::STATUS_INACTIVE) {
                if (!$integration->getId()) {
                    $integration = $this->hexaSyncManagement->generateIntegration();
                }
                $accessToken = $this->hexaSyncManagement->generateToken($integration);
                if ($accessToken) {
                    if (!$this->hexaSyncManagement->activateIntegration($integration)) {
                        throw new \Exception("Activation failed, please try again on System -> Integration");
                    }
                } else {
                    throw new \Exception("Can not generate access token, please try again on System -> Integration");
                }
            }
            $hexaSyncData = $this->hexaSyncManagement->register($integration->getId(), $storeId);
            $encryptString = $this->hexaSyncManagement->encrypt($hexaSyncData);
            $result['success'] = true;
            $result['encrypt'] = urlencode(base64_encode($encryptString));
        } catch (\Exception $e) {
            $result['errorMessage'] = $e->getMessage();
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
