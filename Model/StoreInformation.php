<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

class StoreInformation implements \Beehexa\HexaSync\Api\StoreInformationInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\Information|mixed
     */
    protected $storeInformation;

    /**
     * @var \Beehexa\HexaSync\Api\Data\StoreInformationDataInterfaceFactory
     */
    protected $informationDataFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Beehexa\HexaSync\Api\Data\StoreInformationDataInterfaceFactory $storeInformationDataInterfaceFactory,
        ?\Magento\Store\Model\Information $storeInformation = null
    ) {
        $this->_storeManager = $storeManager;
        $this->informationDataFactory = $storeInformationDataInterfaceFactory;
        $this->storeInformation = $storeInformation ?:
            ObjectManager::getInstance()->get(\Magento\Store\Model\Information::class);
    }

    /**
     * @inheritDoc
     */
    public function getList()
    {
        $stores = $this->_storeManager->getStores();
        $storeInformationList = [];
        foreach ($stores as $store) {
            $information = $this->storeInformation->getStoreInformationObject($store);
            $storeInformation = $this->informationDataFactory->create(['data' => $information->getData()]);
            $storeInformation->setStoreId($store->getId());
            $storeInformation->setStoreCode($store->getCode());
            $storeInformationList[] = $storeInformation;
        }
        return $storeInformationList;
    }

    /**
     * @inheritDoc
     */
    public function get($storeId)
    {
        $store = $this->_storeManager->getStore($storeId);
        $information = $this->storeInformation->getStoreInformationObject($store);
        $storeInformation = $this->informationDataFactory->create(['data' => $information->getData()]);

        $storeInformation->setStoreId($store->getId());
        $storeInformation->setStoreCode($store->getCode());
        return $storeInformation;
    }
}
