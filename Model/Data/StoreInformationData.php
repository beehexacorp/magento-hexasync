<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model\Data;

use Beehexa\HexaSync\Api\Data\StoreInformationDataInterface;
use Magento\Framework\DataObject;

class StoreInformationData extends DataObject implements StoreInformationDataInterface
{

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->getData(self::FIELD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPhone(): ?string
    {
        return $this->getData(self::FIELD_PHONE);
    }

    /**
     * @inheritDoc
     */
    public function getHours(): ?string
    {
        return $this->getData(self::FIELD_HOURS);
    }

    /**
     * @inheritDoc
     */
    public function getStreetLine1(): ?string
    {
        return $this->getData(self::FIELD_STREET_LINE1);
    }

    /**
     * @inheritDoc
     */
    public function getStreetLine2(): ?string
    {
        return $this->getData(self::FIELD_STREET_LINE2);
    }

    /**
     * @inheritDoc
     */
    public function getCity(): ?string
    {
        return $this->getData(self::FIELD_CITY);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode(): ?string
    {
        return $this->getData(self::FIELD_POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function getRegionId(): ?string
    {
        return $this->getData(self::FIELD_REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId(): ?string
    {
        return $this->getData(self::FIELD_COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getVatNumber(): ?string
    {
        return $this->getData(self::FIELD_VAT_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId(): ?string
    {
        return $this->getData(self::FIELD_STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStoreCode(): ?string
    {
        return $this->getData(self::FIELD_STORE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setName($name): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setPhone($phone): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_PHONE, $phone);
    }

    /**
     * @inheritDoc
     */
    public function setHours($hours): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_HOURS, $hours);
    }

    /**
     * @inheritDoc
     */
    public function setStreetLine1($street_line1): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_STREET_LINE1, $street_line1);
    }

    /**
     * @inheritDoc
     */
    public function setStreetLine2($street_line2): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_STREET_LINE2, $street_line2);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($region_id): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_REGION_ID, $region_id);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($country_id): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_COUNTRY_ID, $country_id);
    }

    /**
     * @inheritDoc
     */
    public function setVatNumber($vat_number): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_VAT_NUMBER, $vat_number);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function setStoreCode($storeCode): StoreInformationDataInterface
    {
        return $this->setData(self::FIELD_STORE_CODE, $storeCode);
    }
}
