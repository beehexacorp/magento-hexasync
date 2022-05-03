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
     * @inheirtDoc
     */
    public function getAccount(): ?string
    {
        return $this->getData(self::ACCOUNT);
    }

    /**
     * @inheirtDoc
     */
    public function setAccount(?string $account): void
    {
        $this->setData(self::ACCOUNT, $account);
    }

    /**
     * @inheirtDoc
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheirtDoc
     */
    public function setStatus(?string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheirtDoc
     */
    public function getStoreName(): ?string
    {
        return $this->getData(self::STORE_NAME);
    }

    /**
     * @inheirtDoc
     */
    public function setStoreName(?string $storeName): void
    {
        $this->setData(self::STORE_NAME, $storeName);
    }

    /**
     * @inheirtDoc
     */
    public function getVersion(): ?string
    {
        return $this->getData(self::VERSION);
    }

    /**
     * @inheirtDoc
     */
    public function setVersion(?string $version): void
    {
        $this->setData(self::VERSION, $version);
    }
}
