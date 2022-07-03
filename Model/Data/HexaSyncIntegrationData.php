<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model\Data;

use Beehexa\HexaSync\Api\Data\HexaSyncIntegrationDataInterface;
use Magento\Framework\DataObject;

class HexaSyncIntegrationData extends DataObject implements HexaSyncIntegrationDataInterface
{
    /**
     * @inheritDoc
     */
    public function getAccessToken(): ?string
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(?string $accessToken): void
    {
        $this->setData(self::ACCESS_TOKEN, $accessToken);
    }

    /**
     * @inheritDoc
     */
    public function getAccessTokenSecret(): ?string
    {
        return $this->getData(self::ACCESS_TOKEN_SECRET);
    }

    /**
     * @inheritDoc
     */
    public function setAccessTokenSecret(?string $accessTokenSecret): void
    {
        $this->setData(self::ACCESS_TOKEN_SECRET, $accessTokenSecret);
    }

    /**
     * @inheritDoc
     */
    public function getConsumerKey(): ?string
    {
        return $this->getData(self::CONSUMER_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setConsumerKey(?string $consumerKey): void
    {
        $this->setData(self::CONSUMER_KEY, $consumerKey);
    }

    /**
     * @inheritDoc
     */
    public function getConsumerSecret(): ?string
    {
        return $this->getData(self::CONSUMER_SECRET);
    }

    /**
     * @inheritDoc
     */
    public function setConsumerSecret(?string $consumerSecret): void
    {
        $this->setData(self::CONSUMER_SECRET, $consumerSecret);
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl()
    {
        return $this->getData(self::BASE_URL);
    }

    /**
     * @inheritDoc
     */
    public function setBaseUrl($baseURL)
    {
        $this->setData(self::BASE_URL, $baseURL);
    }
}
