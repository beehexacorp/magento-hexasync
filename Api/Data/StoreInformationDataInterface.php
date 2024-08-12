<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Api\Data;

interface StoreInformationDataInterface
{
    const string FIELD_NAME         = 'name';

    const string FIELD_PHONE        = 'phone';

    const string FIELD_HOURS        = 'hours';

    const string FIELD_STREET_LINE1 = 'street_line1';

    const string FIELD_STREET_LINE2 = 'street_line2';

    const string FIELD_CITY         = 'city';

    const string FIELD_POSTCODE     = 'postcode';

    const string FIELD_REGION_ID    = 'region_id';

    const string FIELD_COUNTRY_ID   = 'country_id';

    const string FIELD_VAT_NUMBER   = 'vat_number';

    const string FIELD_STORE_ID     = 'store_id';

    const string FIELD_STORE_CODE   = 'store_code';

    /**
     * @return ?string
     */
    public function getName(): ?string;

    /**
     * @return ?string
     */
    public function getPhone(): ?string;

    /**
     * @return ?string
     */
    public function getHours(): ?string;

    /**
     * @return ?string
     */
    public function getStreetLine1(): ?string;

    /**
     * @return ?string
     */
    public function getStreetLine2(): ?string;

    /**
     * @return ?string
     */
    public function getCity(): ?string;

    /**
     * @return ?string
     */
    public function getPostcode(): ?string;

    /**
     * @return ?string
     */
    public function getRegionId(): ?string;

    /**
     * @return ?string
     */
    public function getCountryId(): ?string;

    /**
     * @return ?string
     */
    public function getVatNumber(): ?string;

    /**
     * @return ?string
     */
    public function getStoreId(): ?string;

    /**
     * @return ?string
     */
    public function getStoreCode(): ?string;

    /**
     * @param $name
     * @return StoreInformationDataInterface
     */
    public function setName($name): \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $phone
     * @return StoreInformationDataInterface
     */
    public function setPhone($phone): \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $hours
     * @return StoreInformationDataInterface
     */
    public function setHours($hours): \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $street_line1
     * @return StoreInformationDataInterface
     */
    public function setStreetLine1($street_line1):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $street_line2
     * @return StoreInformationDataInterface
     */
    public function setStreetLine2($street_line2):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $city
     * @return StoreInformationDataInterface
     */
    public function setCity($city):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $postcode
     * @return StoreInformationDataInterface
     */
    public function setPostcode($postcode):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $region_id
     * @return StoreInformationDataInterface
     */
    public function setRegionId($region_id):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $country_id
     * @return StoreInformationDataInterface
     */
    public function setCountryId($country_id):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $vat_number
     * @return StoreInformationDataInterface
     */
    public function setVatNumber($vat_number):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $storeId
     * @return StoreInformationDataInterface
     */
    public function setStoreId($storeId):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

    /**
     * @param $storeCode
     * @return StoreInformationDataInterface
     */
    public function setStoreCode($storeCode):\Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;

}
