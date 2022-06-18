<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api\Data;

interface HexaSyncIntegrationDataInterface
{
    /**
     * String constants for property names
     */
    const ACCESS_TOKEN        = "access_token";
    const ACCESS_TOKEN_SECRET = "access_token_secret";
    const CONSUMER_KEY        = "consumer_key";
    const CONSUMER_SECRET     = "consumer_secret";
    const BASE_URL            = "base_url";
    const STORE_NAME          = "store_name";
    const STORE_CODE          = "store_code";

    /**
     * Getter for AdminURL.
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Setter for AdminURL
     *
     * @param $baseURL
     * @return void
     */
    public function setBaseUrl($baseURL);

    /**
     * Getter for AccessToken.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * Setter for AccessToken.
     *
     * @param string|null $accessToken
     *
     * @return void
     */
    public function setAccessToken(?string $accessToken): void;

    /**
     * Getter for AccessTokenSerect.
     *
     * @return string|null
     */
    public function getAccessTokenSecret(): ?string;

    /**
     * Setter for AccessTokenSerect.
     *
     * @param string|null $accessTokenSecret
     *
     * @return void
     */
    public function setAccessTokenSecret(?string $accessTokenSecret): void;

    /**
     * Getter for ConsumerKey.
     *
     * @return string|null
     */
    public function getConsumerKey(): ?string;

    /**
     * Setter for ConsumerKey.
     *
     * @param string|null $consumerKey
     *
     * @return void
     */
    public function setConsumerKey(?string $consumerKey): void;

    /**
     * Getter for ConsumerSerect.
     *
     * @return string|null
     */
    public function getConsumerSecret(): ?string;

    /**
     * Setter for ConsumerSerect.
     *
     * @param string|null $consumerSecret
     *
     * @return void
     */
    public function setConsumerSecret(?string $consumerSecret): void;

}
