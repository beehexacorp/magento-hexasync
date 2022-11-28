<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api\Data;

interface HexaSyncInfoDataInterface
{
    /**
     * String constants for property names
     */
    public const ACCOUNT    = "account";

    public const STATUS     = "status";

    public const STORE_NAME = "store_name";

    public const STORE_CODE = "store_code";

    public const VERSION    = "version";

    /**
     * Getter for Account.
     *
     * @return string|null
     */
    public function getAccount(): ?string;

    /**
     * Setter for Account.
     *
     * @param string|null $account
     *
     * @return void
     */
    public function setAccount(?string $account): void;

    /**
     * Getter for Status.
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Setter for Status.
     *
     * @param string|null $status
     *
     * @return void
     */
    public function setStatus(?string $status): void;

    /**
     * Getter for StoreName.
     *
     * @return string|null
     */
    public function getStoreName(): ?string;

    /**
     * Setter for StoreName.
     *
     * @param string|null $storeName
     *
     * @return void
     */
    public function setStoreName(?string $storeName): void;

    /**
     * Getter for StoreCode.
     *
     * @return string|null
     */
    public function getStoreCode(): ?string;

    /**
     * Setter for StoreCode.
     *
     * @param string|null $storeCode
     *
     * @return void
     */
    public function setStoreCode(?string $storeCode): void;

    /**
     * Getter for Version.
     *
     * @return string|null
     */
    public function getVersion(): ?string;

    /**
     * Setter for Version.
     *
     * @param string|null $version
     *
     * @return void
     */
    public function setVersion(?string $version): void;
}
