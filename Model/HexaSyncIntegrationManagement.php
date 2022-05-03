<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\HexaSyncIntegrationInterface;
use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterfaceFactory;
use Beehexa\Base\Helper\Data as BeehexaData;
use Magento\Config\Model\Config as SystemConfig;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Model\ConfigBasedIntegrationManager;
use Magento\Integration\Model\Integration;
use Magento\Store\Model\StoreManagerInterface;

class HexaSyncIntegrationManagement implements HexaSyncIntegrationInterface
{

    /**
     * @var BeehexaData
     */
    protected $beehexaHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SystemConfig
     */
    private $config;

    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;

    /**
     * @var OauthServiceInterface
     */
    private $oauthService;

    /**
     * @var ConfigBasedIntegrationManager
     */
    private $configBasedIntegrationManager;

    /**
     * IntegrationManager constructor
     *
     * @param SystemConfig                            $config
     * @param BeehexaData                             $beehexaHelper
     * @param StoreManagerInterface                   $storeManager
     * @param IntegrationServiceInterface             $integrationService
     * @param ConfigBasedIntegrationManager           $configBasedIntegrationManager
     * @param HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory
     * @param OauthServiceInterface                   $oauthService
     */
    public function __construct(
        SystemConfig                            $config,
        BeehexaData                             $beehexaHelper,
        StoreManagerInterface                   $storeManager,
        IntegrationServiceInterface             $integrationService,
        ConfigBasedIntegrationManager           $configBasedIntegrationManager,
        HexaSyncIntegrationDataInterfaceFactory $hexaSyncIntegrationDataInterfaceFactory,
        OauthServiceInterface                   $oauthService
    ) {
        $this->integrationService = $integrationService;
        $this->beehexaHelper = $beehexaHelper;
        $this->hexaSyncIntegrationDataInterfaceFactory = $hexaSyncIntegrationDataInterfaceFactory;
        $this->configBasedIntegrationManager = $configBasedIntegrationManager;
        $this->config = $config;
        $this->oauthService = $oauthService;
        $this->storeManager = $storeManager;
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
        $this->integrationService->update($integrationData);
        return true;
    }

    /**
     * This method execute Generate Token command and enable integration
     *
     * @return bool|\Magento\Integration\Model\Oauth\Token
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function generateToken()
    {
        $consumerId = $this->generateIntegration()->getConsumerId();
        $accessToken = $this->oauthService->getAccessToken($consumerId);
        if (!$accessToken && $this->oauthService->createAccessToken($consumerId, true)) {
            $accessToken = $this->oauthService->getAccessToken($consumerId);
        }
        return $accessToken;
    }

    /**
     * Returns consumer Id for Hexasync integration user
     *
     * @return \Magento\Integration\Model\Integration
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function generateIntegration()
    {
        $integration = $this->getIntegration();
        if (!$integration->getId()) {
            $integrationName = $this->config->getConfigDataValue('hexasync/integration_name');
            $integration = $this->integrationService->create($this->getIntegrationData($integrationName));
        }
        return $integration;
    }

    /**
     * @return Integration
     */
    public function getIntegration()
    {
        $integrationName = $this->config->getConfigDataValue('hexasync/integration_name');
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
     * @inheirtDoc
     */
    public function generate()
    {
        $integration = $this->getIntegration();
        if ($integration->getId()) {
            throw new AlreadyExistsException(__("The integration '%1' already exists", $integration->getName()));
        }
        $integration = $this->generateIntegration();
        return $integration->getId();
    }

    /**
     * @inheirtDoc
     */
    public function getByName($name)
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

}
