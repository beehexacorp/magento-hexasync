<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api\Data;

/**
 * Store interface.
 * @api
 * @since 102.0.0
 */
interface StoreInformationDataInterface
{
    /**#@+
     * Constants
     */
    public const FIELD_NAME = 'name';
    public const FIELD_PHONE = 'phone';
    public const FIELD_HOURS = 'hours';
    public const FIELD_STREET_LINE1 = 'street_line1';
    public const FIELD_STREET_LINE2 = 'street_line2';
    public const FIELD_CITY = 'city';
    public const FIELD_POSTCODE = 'postcode';
    public const FIELD_REGION_ID = 'region_id';
    public const FIELD_COUNTRY_ID = 'country_id';
    public const FIELD_VAT_NUMBER = 'vat_number';
    public const FIELD_STORE_ID = 'store_id';
    public const FIELD_STORE_CODE = 'store_code';

    /**
     * Get Store Name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Phone
     *
     * @return string
     */
    public function getPhone();

    /**
     * Get hours
     *
     * @return string
     */
    public function getHours();

    /**
     * Get Street Line 1
     *
     * @return string
     */
    public function getStreetLine1();

    /**
     * Get Street Line 2
     *
     * @return string
     */
    public function getStreetLine2();

    /**
     * Get City
     *
     * @return string
     */
    public function getCity();

    /**
     * Get PostCode
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Get Region Id
     *
     * @return string
     */
    public function getRegionId();

    /**
     * Get CountryId
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Get Vat Number
     *
     * @return string
     */
    public function getVatNumber();

    /**
     * Get StoreId
     *
     * @return string
     */
    public function getStoreId();

    /**
     * Get Store Code
     *
     * @return string
     */
    public function getStoreCode();

    /**
     * Set Store Name
     *
     * @param string $name
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setName($name);

    /**
     * Set Phone
     *
     * @param int $phone
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setPhone($phone);

    /**
     * Set Hours
     *
     * @param int $hours
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setHours($hours);

    /**
     * Set Street Line 1
     *
     * @param string $street_line1
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStreetLine1($street_line1);

    /**
     * Set Street Line 2
     *
     * @param string $street_line2
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStreetLine2($street_line2);

    /**
     * Set City
     *
     * @param string $city
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setCity($city);

    /**
     * Set PostCode
     *
     * @param string $postcode
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setPostcode($postcode);

    /**
     * Set RegionId
     *
     * @param string $region_id
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setRegionId($region_id);

    /**
     * Set CountryId
     *
     * @param string $country_id
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setCountryId($country_id);

    /**
     * Set Vat Number
     *
     * @param string $vat_number
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setVatNumber($vat_number);

    /**
     * Set StoreId
     *
     * @param int $storeId
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStoreId($storeId);

    /**
     * Set Store Code
     *
     * @param int $storeCode
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStoreCode($storeCode);
}
