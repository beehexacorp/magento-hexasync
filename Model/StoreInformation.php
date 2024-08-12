<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\Data\StoreInformationDataInterfaceFactory;
use Beehexa\HexaSync\Api\StoreInformationInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;

class StoreInformation implements StoreInformationInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Information|mixed
     */
    protected $storeInformation;

    /**
     * @var StoreInformationDataInterfaceFactory
     */
    protected $informationDataFactory;

    public function __construct(
        StoreManagerInterface                                           $storeManager,
        StoreInformationDataInterfaceFactory $storeInformationDataInterfaceFactory,
        ?Information $storeInformation = null
    ) {
        $this->_storeManager = $storeManager;
        $this->informationDataFactory = $storeInformationDataInterfaceFactory;
        $this->storeInformation = $storeInformation ?:
            ObjectManager::getInstance()->get(Information::class);
    }

    /**
     * @inheritDoc
     */
    public function get($storeId): \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
    {
        $store = $this->_storeManager->getStore($storeId);
        $information = $this->storeInformation->getStoreInformationObject($store);
        $storeInformation = $this->informationDataFactory->create(['data' => $information->getData()]);

        $storeInformation->setStoreId($store->getId());
        $storeInformation->setStoreCode($store->getCode());
        return $storeInformation;
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
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
}
