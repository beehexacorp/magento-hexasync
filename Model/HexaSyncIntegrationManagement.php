<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;
use Beehexa\HexaSync\Api\HexaSyncIntegrationInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory;
use Beehexa\Base\Helper\Data as BeehexaData;
use Magento\Config\Model\Config as SystemConfig;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Model\Integration;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Beehexa\HexaSync\Model\Context as HexaSyncContext;

class HexaSyncIntegrationManagement implements HexaSyncIntegrationInterface
{
    protected $_eventPrefix = 'beehexa_hexasync';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SystemConfig
     */
    private $systemConfig;

    /**
     * @var ConfigInterface
     */
    private $storeConfigManager;

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
     */
    public function __construct(
        HexaSyncContext             $context,
        EventManager                $eventManager,
        SystemConfig                $systemConfig,
        ConfigInterface             $configManager,
        StoreManagerInterface       $storeManager,
        IntegrationServiceInterface $integrationService
    ) {
        $this->integrationService = $integrationService;
        $this->hexaSyncIntegrationDataInterfaceFactory = $context->getHexaSyncIntegrationDataInterfaceFactory();
        $this->systemConfig = $systemConfig;
        $this->storeManager = $storeManager;
        $this->storeConfigManager = $configManager;
        $this->eventManager = $eventManager;
    }

    /**
     * Activate predefined integration user
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function activateIntegration()
    {
        $integration = $this->getIntegration();
        if (!$integration->getId()) {
            throw new NoSuchEntityException(__('Cannot find predefined integration user!'));
        }
        $integrationData = $this->getIntegrationData($integration->getName(), Integration::STATUS_ACTIVE);
        unset($integrationData['entity_id']);
        $integrationData['integration_id'] = $integration->getId();
        $this->eventManager->dispatch($this->_eventPrefix .  '_active_before', ['integration' =>  $integration]);
        $this->integrationService->update($integrationData);
        $this->eventManager->dispatch($this->_eventPrefix .  '_active_after', ['integration' =>  $integration]);
        return true;
    }


    /**
     * Returns consumer Id for Hexasync integration user
     *
     * @return \Magento\Integration\Model\Integration
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    private function _generateIntegration()
    {
        $integration = $this->getIntegration();
        if (!$integration->getId()) {
            $integrationName = $this->systemConfig->getConfigDataValue('hexasync/integration_name');
            $integrationData = $this->getIntegrationData($integrationName);
            $this->eventManager->dispatch($this->_eventPrefix .  '_generate_before', ['integration_data' =>  $integrationData]);
            $integration = $this->integrationService->create($integrationData);
            $this->eventManager->dispatch($this->_eventPrefix .  '_generate_after', ['integration' =>  $integration]);
        }
        return $integration;
    }

    /**
     * @return Integration
     */
    public function getIntegration()
    {
        $integrationName = $this->systemConfig->getConfigDataValue('hexasync/integration_name');
        $integration = $this->integrationService->findByName($integrationName);
        return $integration;
    }

    /**
     * Register store
     *
     * @inheirtDoc
     */
    public function register(?string $storeCode)
    {
        $store = $this->storeManager->getStore($storeCode);
        $storeURL = $store->getBaseUrl();
        $storeName = $store->getName();
        $websiteId = $store->getWebsiteId();
        $integration = $this->getIntegration();
        if (!$integration->getId()) {
            throw new NoSuchEntityException(__('Cannot find predefined integration user!'));
        }
        //Implement later.
        $hexaSyncData = $this->hexaSyncIntegrationDataInterfaceFactory->create();
        return $hexaSyncData;
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
        $integrationData = [
            'name'              => $integrationName,
            'status'            => $status,
            'all_resources'     => true,
            'endpoint'          => 'https://www.beehexa.com/test/callback.php',
            'identity_link_url' => 'https://www.beehexa.com/test/login.php',
            'resource'          => [],
        ];
        return $integrationData;
    }

    /**
     * @inheirtDoc
     */
    public function getByName($name): HexaSyncIntegrationDataInterface
    {
        $integration = $this->integrationService->findByName($name);
        //Implement later.
        $hexaSyncData = $this->hexaSyncIntegrationDataInterfaceFactory->create(['data' => [
            'access_token'        => $integration->getData('access_token'),
            'access_token_secret' => $integration->getData('access_token_secret'),
            'consumer_key'        => $integration->getData('consumer_key'),
            'consumer_secret'     => $integration->getData('consumer_secret')]
        ]);
        return $hexaSyncData;
    }

    /**
     * @inheirtDoc
     */
    public function generateIntegration(): int
    {
        $integration = $this->getIntegration();
        if ($integration->getId()) {
            throw new AlreadyExistsException(__("The integration '%1' already exists", $integration->getName()));
        }
        $integration = $this->_generateIntegration();
        return $integration->getId();
    }

    /**
     * @inheirtDoc
     */
    public function saveConnectorInfo(HexaSyncInfoDataInterface $connector): HexaSyncInfoDataInterface
    {
        $this->storeConfigManager->saveConfig(BeehexaData::XML_CONFIG_PREFIX . '/connector/account', $connector->getAccount());
        $this->storeConfigManager->saveConfig(BeehexaData::XML_CONFIG_PREFIX . '/connector/status', $connector->getStatus());
        $this->storeConfigManager->saveConfig(BeehexaData::XML_CONFIG_PREFIX . '/connector/store_name', $connector->getStoreName());
        $this->storeConfigManager->saveConfig(BeehexaData::XML_CONFIG_PREFIX . '/connector/version', $connector->getVersion());
        return $connector;
    }
}
