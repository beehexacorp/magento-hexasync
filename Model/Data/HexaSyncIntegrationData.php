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
     * Getter for AccessToken.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * Setter for AccessToken.
     *
     * @param string|null $accessToken
     *
     * @return void
     */
    public function setAccessToken(?string $accessToken): void
    {
        $this->setData(self::ACCESS_TOKEN, $accessToken);
    }

    /**
     * Getter for AccessTokenSerect.
     *
     * @return string|null
     */
    public function getAccessTokenSecret(): ?string
    {
        return $this->getData(self::ACCESS_TOKEN_SECRET);
    }

    /**
     * Setter for AccessTokenSerect.
     *
     * @param string|null $accessTokenSecret
     *
     * @return void
     */
    public function setAccessTokenSecret(?string $accessTokenSecret): void
    {
        $this->setData(self::ACCESS_TOKEN_SECRET, $accessTokenSecret);
    }

    /**
     * Getter for ConsumerKey.
     *
     * @return string|null
     */
    public function getConsumerKey(): ?string
    {
        return $this->getData(self::CONSUMER_KEY);
    }

    /**
     * Setter for ConsumerKey.
     *
     * @param string|null $consumerKey
     *
     * @return void
     */
    public function setConsumerKey(?string $consumerKey): void
    {
        $this->setData(self::CONSUMER_KEY, $consumerKey);
    }

    /**
     * Getter for ConsumerSerect.
     *
     * @return string|null
     */
    public function getConsumerSecret(): ?string
    {
        return $this->getData(self::CONSUMER_SECRET);
    }

    /**
     * Setter for ConsumerSerect.
     *
     * @param string|null $consumerSecret
     *
     * @return void
     */
    public function setConsumerSecret(?string $consumerSecret): void
    {
        $this->setData(self::CONSUMER_SECRET, $consumerSecret);
    }
}
