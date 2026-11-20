<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Controller\Adminhtml\Integration;

use Beehexa\HexaSync\Helper\Data as HexaSyncHelper;
use Beehexa\HexaSync\Model\OauthHandshake;
use Exception;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\Integration;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Starts the OAuth handshake with HexaSync for the "Register Store" button
 */
class Register extends BackendAction implements HttpPostActionInterface
{
    /**
     * Registering hands out API access, so it needs the same permission as managing integrations
     */
    public const ADMIN_RESOURCE = 'Magento_Integration::integrations';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var OauthHandshake
     */
    private OauthHandshake $oauthHandshake;

    /**
     * @var HexaSyncHelper
     */
    private HexaSyncHelper $hexaSyncHelper;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param OauthHandshake $oauthHandshake
     * @param HexaSyncHelper $hexaSyncHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        OauthHandshake        $oauthHandshake,
        HexaSyncHelper        $hexaSyncHelper,
        LoggerInterface       $logger
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->oauthHandshake = $oauthHandshake;
        $this->hexaSyncHelper = $hexaSyncHelper;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = [
            'success' => false,
            'errorMessage' => '',
        ];
        try {
            $serviceEndpoint = (string)$this->getRequest()->getParam('service_endpoint')
                ?: (string)$this->hexaSyncHelper->getServiceEndpoint();
            $handshake = $this->oauthHandshake->start($serviceEndpoint, $this->getStoreToRegister());

            $result['success'] = true;
            $result[Integration::IDENTITY_LINK_URL] = $handshake[Integration::IDENTITY_LINK_URL];
            $result['oauth_consumer_key'] = $handshake['oauth_consumer_key'];
            // Magento's own page for the end of the handshake: it closes the popup.
            $result['success_call_back'] = $this->getUrl('adminhtml/integration/loginSuccessCallback');
        } catch (LocalizedException $e) {
            $result['errorMessage'] = $e->getMessage();
        } catch (Exception $e) {
            $this->logger->critical($e);
            $result['errorMessage'] = (string)__(
                'Could not start the registration. See the exception log for details.'
            );
        }
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }

    /**
     * Store view the admin is configuring; the default store view when no scope is selected
     *
     * @return StoreInterface
     * @throws LocalizedException
     */
    private function getStoreToRegister(): StoreInterface
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId) {
            return $this->storeManager->getStore($storeId);
        }
        $websiteId = (int)$this->getRequest()->getParam('website', 0);
        $store = $websiteId
            ? $this->storeManager->getWebsite($websiteId)->getDefaultStore()
            : $this->storeManager->getDefaultStoreView();
        if (!$store) {
            throw new LocalizedException(__('There is no store view to register.'));
        }
        return $store;
    }
}
