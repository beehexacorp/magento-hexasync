<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterfaceFactory;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory;
use Beehexa\HexaSync\Api\HexaSyncIntegrationInterface;
use Beehexa\HexaSync\Encryption\EncryptorInterface;
use Beehexa\HexaSync\Model\Context as HexaSyncContext;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Model\Config as SystemConfig;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Model\Integration;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class HexaSyncIntegrationManagement implements HexaSyncIntegrationInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'beehexa_hexasync';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var HexaSyncIntegrationDataInterfaceFactory
     */
    protected $hexaSyncIntegrationDataInterfaceFactory;
    /**
     * @var HexaSyncInfoDataInterfaceFactory
     */
    protected $hexaSyncInfoDataInterfaceFactory;
    /**
     * @var UrlInterface
     */
    protected $backendUrl;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var OauthServiceInterface
     */
    protected $oauthService;
    /**
     * @var \Beehexa\HexaSync\Helper\RegisterInformation
     */
    protected $registerHelper;
    /**
     * @var SystemConfig
     */
    private $systemConfig;
    /**
     * @var ConfigInterface
     */
    private $configManager;
    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * IntegrationManager constructor
     *
     * @param HexaSyncContext             $context
     * @param EventManager                $eventManager
     * @param SystemConfig                $systemConfig
     * @param ConfigInterface             $configManager
     * @param StoreManagerInterface       $storeManager
     * @param IntegrationServiceInterface $integrationService
     * @param EncryptorInterface          $encryptor
     * @param OauthServiceInterface       $oauthService
     * @param UrlInterface                $backendUrl
     */
    public function __construct(
        HexaSyncContext             $context,
        EventManager                $eventManager,
        SystemConfig                $systemConfig,
        ConfigInterface             $configManager,
        StoreManagerInterface       $storeManager,
        IntegrationServiceInterface $integrationService,
        EncryptorInterface          $encryptor,
        OauthServiceInterface       $oauthService,
        UrlInterface                $backendUrl
    ) {
        $this->integrationService = $integrationService;
        $this->hexaSyncIntegrationDataInterfaceFactory = $context->getHexaSyncIntegrationDataInterfaceFactory();
        $this->hexaSyncInfoDataInterfaceFactory = $context->getHexaSyncInfoDataInterfaceFactory();
        $this->registerHelper = $context->getRegisterHelper();
        $this->systemConfig = $systemConfig;
        $this->storeManager = $storeManager;
        $this->configManager = $configManager;
        $this->eventManager = $eventManager;
        $this->backendUrl = $backendUrl;
        $this->encryptor = $encryptor;
        $this->oauthService = $oauthService;
    }

    /**
     * @inheritDoc
     */
    public function activateIntegration($integration = null)
    {
        if (null == $integration) {
            $integration = $this->getIntegration();
        }
        if (!$integration->getId()) {
            throw new NoSuchEntityException(__('Cannot find predefined integration user!'));
        }
        $integrationData = $this->getIntegrationData($integration->getName(), Integration::STATUS_ACTIVE);
        unset($integrationData['entity_id']);
        $integrationData['integration_id'] = $integration->getId();
        $this->eventManager->dispatch($this->_eventPrefix . '_active_before', ['integration' => $integration]);
        $this->integrationService->update($integrationData);
        $this->eventManager->dispatch($this->_eventPrefix . '_active_after', ['integration' => $integration]);
        return true;
    }

    /**
     * Getter for integration
     *
     * @return Integration
     */
    public function getIntegration()
    {
        $integrationName = $this->systemConfig->getConfigDataValue('hexasync/integration_name');
        return $this->integrationService->findByName($integrationName);
    }

    /**
     * Returns default attributes for MA integration user
     *
     * @param string $integrationName
     * @param int    $status
     * @return array
     */
    private function getIntegrationData($integrationName, $status = Integration::STATUS_INACTIVE)
    {
        return [
            'name'          => $integrationName,
            'status'        => $status,
            'all_resources' => true,
            // Remove this because credential will be published to Hexasync by http request.
            //            'endpoint'          => 'https://app.hexasync.com/callback/magento',
            //            'identity_link_url' => 'https://app.hexasync.com/callback/magento',
            'resource'      => [],
        ];
    }

    /**
     * Register Integration
     *
     * @param int         $integrationId
     * @param string|null $storeCode
     * @return HexaSyncIntegrationDataInterface
     * @throws NoSuchEntityException
     */
    public function register($integrationId, ?string $storeCode)
    {
        $store = $this->storeManager->getStore($storeCode);
        $storeName = $store->getName();
        $storeCode = $store->getCode();
        $storeURL = $store->getBaseUrl();
        $websiteId = $store->getWebsiteId();
        if (!$integrationId) {
            throw new NoSuchEntityException(__('Cannot find predefined integration user!'));
        }
        //Reload integration for getting consumer and access
        $integration = $this->integrationService->get($integrationId);
        return $this->hexaSyncIntegrationDataInterfaceFactory->create(['data' => [
            'access_token'        => $integration->getData('token'),
            'access_token_secret' => $integration->getData('token_secret'),
            'consumer_key'        => $integration->getData('consumer_key'),
            'consumer_secret'     => $integration->getData('consumer_secret'),
            'base_url'            => $this->getAdminUrl(),
            'store_name'          => $storeName,
            'store_code'          => $storeCode,
        ]]);
    }

    /**
     * Getter for admin URL
     *
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->backendUrl->getRouteUrl();
    }

    /**
     * This method execute Generate Token command and enable integration
     *
     * @param Integration $integration
     * @return bool|\Magento\Integration\Model\Oauth\Token
     */
    public function generateToken($integration)
    {
        $consumerId = $integration->getConsumerId();
        $accessToken = $this->oauthService->getAccessToken($consumerId);
        if (!$accessToken && $this->oauthService->createAccessToken($consumerId, true)) {
            $accessToken = $this->oauthService->getAccessToken($consumerId);
        }
        return $accessToken;
    }

    /**
     * @inheritDoc
     */
    public function encrypt($hexaSyncData)
    {
        /**
         * @var $hexaSyncData \Magento\Framework\DataObject
         */
        $hexaSyncDataString = $hexaSyncData->toJson();
        $encrypted = $this->encryptor->encrypt($hexaSyncDataString);
        return $encrypted;
    }

    /**
     * @inheritDoc
     */
    public function getByName($name): HexaSyncIntegrationDataInterface
    {
        $integration = $this->integrationService->findByName($name);
        //Implement later.
        return $this->hexaSyncIntegrationDataInterfaceFactory->create(['data' => [
            'access_token'        => $integration->getData('access_token'),
            'access_token_secret' => $integration->getData('access_token_secret'),
            'consumer_key'        => $integration->getData('consumer_key'),
            'consumer_secret'     => $integration->getData('consumer_secret')]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function generateIntegration()
    {
        $integration = $this->getIntegration();
        if ($integration->getId()) {
            throw new AlreadyExistsException(__("The integration '%1' already exists", $integration->getName()));
        }
        $integration = $this->_generateIntegration();
        return $integration;
    }

    /**
     * Generate Integration
     *
     * @return Integration
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    private function _generateIntegration()
    {
        $integration = $this->getIntegration();
        if (!$integration->getId()) {
            $integrationName = $this->systemConfig->getConfigDataValue('hexasync/integration_name');
            $integrationData = $this->getIntegrationData($integrationName);
            $this->eventManager->dispatch($this->_eventPrefix . '_generate_before', [
                'integration_data' => $integrationData]);
            $integration = $this->integrationService->create($integrationData);
            $this->eventManager->dispatch($this->_eventPrefix . '_generate_after', [
                'integration' => $integration]);
        }
        return $integration;
    }

    /**
     * @inheritDoc
     */
    public function saveConnectorInfo(HexaSyncInfoDataInterface $connector): HexaSyncInfoDataInterface
    {
        $store = $this->storeManager->getStore($connector->getStoreCode());
        $scopeCode = ScopeInterface::SCOPE_STORE;
        $storeId = $store->getId();
        $prefix = 'beehexa/connector/';
        $this->configManager->saveConfig($prefix . 'account', $connector->getAccount(), $scopeCode, $storeId);
        $this->configManager->saveConfig($prefix . 'status', $connector->getStatus(), $scopeCode, $storeId);
        $this->configManager->saveConfig($prefix . 'store_name', $connector->getStoreName(), $scopeCode, $storeId);
        $this->configManager->saveConfig($prefix . 'version', $connector->getVersion(), $scopeCode, $storeId);
        return $connector;
    }

    /**
     * @inheritDoc
     */
    public function getConnectorInfo(string $storeId = null): \Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface
    {
        $registerData = [];
        $registerData['account'] = $this->registerHelper->getAccount(ScopeInterface::SCOPE_STORE, $storeId);
        $registerData['status'] = $this->registerHelper->getStatus(ScopeInterface::SCOPE_STORE, $storeId);
        $registerData['store_name'] = $this->registerHelper->getStoreName(ScopeInterface::SCOPE_STORE, $storeId);
        $registerData['version'] = $this->registerHelper->getAPIVersion(ScopeInterface::SCOPE_STORE, $storeId);

        return $this->hexaSyncInfoDataInterfaceFactory->create([
            'data' => $registerData
        ]);
    }
}
