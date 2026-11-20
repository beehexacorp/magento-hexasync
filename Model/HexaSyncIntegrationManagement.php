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
use Beehexa\HexaSync\Helper\RegisterInformation;
use Beehexa\HexaSync\Model\Context as HexaSyncContext;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Config\Model\Config as SystemConfig;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Model\Integration;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class HexaSyncIntegrationManagement implements HexaSyncIntegrationInterface
{
    /**
     * @var string
     */
    protected string $_eventPrefix = 'beehexa_hexasync';

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var HexaSyncIntegrationDataInterfaceFactory
     */
    protected HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory;

    /**
     * @var HexaSyncInfoDataInterfaceFactory
     */
    protected HexaSyncInfoDataInterfaceFactory $hexaSyncInfoDataInterfaceFactory;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $backendUrl;

    /**
     * @var OauthServiceInterface
     */
    protected OauthServiceInterface $oauthService;

    /**
     * @var RegisterInformation
     */
    protected RegisterInformation $registerHelper;

    /**
     * @var SystemConfig
     */
    private SystemConfig $systemConfig;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $configManager;

    /**
     * @var IntegrationServiceInterface
     */
    private IntegrationServiceInterface $integrationService;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var ReinitableConfigInterface
     */
    private ReinitableConfigInterface $reinitableConfig;

    /**
     * IntegrationManager constructor
     *
     * @param HexaSyncContext $context
     * @param EventManager $eventManager
     * @param SystemConfig $systemConfig
     * @param ConfigInterface $configManager
     * @param StoreManagerInterface $storeManager
     * @param IntegrationServiceInterface $integrationService
     * @param OauthServiceInterface $oauthService
     * @param UrlInterface $backendUrl
     * @param TypeListInterface $cacheTypeList
     * @param ReinitableConfigInterface $reinitableConfig
     */
    public function __construct(
        HexaSyncContext             $context,
        EventManager                $eventManager,
        SystemConfig                $systemConfig,
        ConfigInterface             $configManager,
        StoreManagerInterface       $storeManager,
        IntegrationServiceInterface $integrationService,
        OauthServiceInterface       $oauthService,
        UrlInterface                $backendUrl,
        TypeListInterface           $cacheTypeList,
        ReinitableConfigInterface   $reinitableConfig
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
        $this->oauthService = $oauthService;
        $this->cacheTypeList = $cacheTypeList;
        $this->reinitableConfig = $reinitableConfig;
    }

    /**
     * @inheritDoc
     */
    public function activateIntegration(?Integration $integration): bool
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
     * @param int $status
     * @return array
     */
    private function getIntegrationData($integrationName, $status = Integration::STATUS_INACTIVE)
    {
        return [
            'name' => $integrationName,
            'status' => $status,
            'all_resources' => true,
            // Remove this because credential will be published to HexaSync by http request.
            //            'endpoint'          => 'https://app.hexasync.com/callback/magento',
            //            'identity_link_url' => 'https://app.hexasync.com/callback/magento',
            'resource' => [],
        ];
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
     * Point the integration at the HexaSync OAuth endpoints and reset it for a new handshake
     *
     * Any existing token is removed and the integration goes back to inactive, exactly as Magento does when an
     * admin clicks "Reauthorize". Magento activates it again once HexaSync finishes the token exchange.
     *
     * @param Integration $integration
     * @param string $endpoint URL Magento posts the consumer credentials to
     * @param string $identityLinkUrl URL opened in the popup for the HexaSync login
     * @return Integration
     * @throws IntegrationException
     */
    public function prepareForOauthHandshake(
        Integration $integration,
        string $endpoint,
        string $identityLinkUrl
    ): Integration {
        $this->oauthService->deleteIntegrationToken($integration->getConsumerId());
        $integrationData = $this->getIntegrationData($integration->getName(), Integration::STATUS_INACTIVE);
        $integrationData['integration_id'] = $integration->getId();
        $integrationData['endpoint'] = $endpoint;
        $integrationData['identity_link_url'] = $identityLinkUrl;
        return $this->integrationService->update($integrationData);
    }

    /**
     * @inheritDoc
     */
    public function getByName($name): HexaSyncIntegrationDataInterface
    {
        $integration = $this->integrationService->findByName($name);
        //Implement later.
        return $this->hexaSyncIntegrationDataInterfaceFactory->create([
            'data' => [
                'access_token' => $integration->getData('access_token'),
                'access_token_secret' => $integration->getData('access_token_secret'),
                'consumer_key' => $integration->getData('consumer_key'),
                'consumer_secret' => $integration->getData('consumer_secret')
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function generateIntegration(): Integration
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
     * @throws IntegrationException
     */
    private function _generateIntegration()
    {
        $integration = $this->getIntegration();
        if (!$integration->getId()) {
            $integrationName = $this->systemConfig->getConfigDataValue('hexasync/integration_name');
            $integrationData = $this->getIntegrationData($integrationName);
            $this->eventManager->dispatch($this->_eventPrefix . '_generate_before', [
                'integration_data' => $integrationData
            ]);
            $integration = $this->integrationService->create($integrationData);
            $this->eventManager->dispatch($this->_eventPrefix . '_generate_after', [
                'integration' => $integration
            ]);
        }
        return $integration;
    }

    /**
     * @inheritDoc
     */
    public function saveConnectorInfo(HexaSyncInfoDataInterface $connector): HexaSyncInfoDataInterface
    {
        $store = $this->storeManager->getStore($connector->getStoreCode());
        // core_config_data stores store-view values under "stores"; rows written as "store" are never read back.
        $scopeCode = ScopeInterface::SCOPE_STORES;
        $storeId = $store->getId();
        $prefix = 'beehexa/connector/';
        $this->configManager->saveConfig($prefix . 'account', $connector->getAccount(), $scopeCode, $storeId);
        $this->configManager->saveConfig($prefix . 'status', $connector->getStatus(), $scopeCode, $storeId);
        $this->configManager->saveConfig($prefix . 'store_name', $connector->getStoreName(), $scopeCode, $storeId);
        $this->configManager->saveConfig($prefix . 'version', $connector->getVersion(), $scopeCode, $storeId);
        // Without this the admin keeps showing the previous registration until the config cache is flushed.
        $this->cacheTypeList->cleanType(ConfigCache::TYPE_IDENTIFIER);
        $this->reinitableConfig->reinit();
        return $connector;
    }

    /**
     * @inheritDoc
     */
    public function getConnectorInfo(?string $storeId): HexaSyncInfoDataInterface
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

    public function setConfigManager(ConfigInterface $configManager): HexaSyncIntegrationManagement
    {
        $this->configManager = $configManager;
        return $this;
    }
}
