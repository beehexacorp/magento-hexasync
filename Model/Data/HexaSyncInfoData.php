<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model\Data;

use Beehexa\HexaSync\Api\Data\HexaSyncInfoDataInterface;
use Magento\Framework\DataObject;

class HexaSyncInfoData extends DataObject implements HexaSyncInfoDataInterface
{
    /**
     * @inheritDoc
     */
    public function getAccount(): ?string
    {
        return $this->getData(self::ACCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setAccount(?string $account): void
    {
        $this->setData(self::ACCOUNT, $account);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(?string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getStoreName(): ?string
    {
        return $this->getData(self::STORE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setStoreName(?string $storeName): void
    {
        $this->setData(self::STORE_NAME, $storeName);
    }

    /**
     * @inheritDoc
     */
    public function getStoreCode(): ?string
    {
        return $this->getData(self::STORE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setStoreCode(?string $storeCode): void
    {
        $this->setData(self::STORE_CODE, $storeCode);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): ?string
    {
        return $this->getData(self::VERSION);
    }

    /**
     * @inheritDoc
     */
    public function setVersion(?string $version): void
    {
        $this->setData(self::VERSION, $version);
    }
}
