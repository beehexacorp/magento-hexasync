<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api\Data;

interface StoreInformationDataInterface
{
    const FIELD_NAME = 'name';
    const FIELD_PHONE = 'phone';
    const FIELD_HOURS = 'hours';
    const FIELD_STREET_LINE1 = 'street_line1';
    const FIELD_STREET_LINE2 = 'street_line2';
    const FIELD_CITY = 'city';
    const FIELD_POSTCODE = 'postcode';
    const FIELD_REGION_ID = 'region_id';
    const FIELD_COUNTRY_ID = 'country_id';
    const FIELD_VAT_NUMBER = 'vat_number';
    const FIELD_STORE_ID = 'store_id';
    const FIELD_STORE_CODE = 'store_code';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @return string
     */
    public function getHours();

    /**
     * @return string
     */
    public function getStreetLine1();

    /**
     * @return string
     */
    public function getStreetLine2();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @return string
     */
    public function getRegionId();

    /**
     * @return string
     */
    public function getCountryId();

    /**
     * @return string
     */
    public function getVatNumber();

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getStoreCode();

    /**
     * @param $name
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setName($name);

    /**
     * @param $phone
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setPhone($phone);

    /**
     * @param $hours
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setHours($hours);

    /**
     * @param $street_line1
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStreetLine1($street_line1);

    /**
     * @param $street_line2
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStreetLine2($street_line2);

    /**
     * @param $city
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setCity($city);

    /**
     * @param $postcode
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setPostcode($postcode);

    /**
     * @param $region_id
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setRegionId($region_id);

    /**
     * @param $country_id
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setCountryId($country_id);

    /**
     * @param $vat_number
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setVatNumber($vat_number);

    /**
     * @param $storeId
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStoreId($storeId);

    /**
     * @param $storeCode
     * @return \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
     */
    public function setStoreCode($storeCode);

}
