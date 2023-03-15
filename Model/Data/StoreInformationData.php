<?php
/*
 * Copyright © 2023 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Model\Data;

use Magento\Framework\DataObject;

class StoreInformationData extends DataObject implements \Beehexa\HexaSync\Api\Data\StoreInformationDataInterface
{

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::FIELD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPhone()
    {
        return $this->getData(self::FIELD_PHONE);
    }

    /**
     * @inheritDoc
     */
    public function getHours()
    {
        return $this->getData(self::FIELD_HOURS);
    }

    /**
     * @inheritDoc
     */
    public function getStreetLine1()
    {
        return $this->getData(self::FIELD_STREET_LINE1);
    }

    /**
     * @inheritDoc
     */
    public function getStreetLine2()
    {
        return $this->getData(self::FIELD_STREET_LINE2);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::FIELD_CITY);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->getData(self::FIELD_POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function getRegionId()
    {
        return $this->getData(self::FIELD_REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->getData(self::FIELD_COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getVatNumber()
    {
        return $this->getData(self::FIELD_VAT_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::FIELD_STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStoreCode()
    {
        return $this->getData(self::FIELD_STORE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return  $this->setData(self::FIELD_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setPhone($phone)
    {
        return $this->setData(self::FIELD_PHONE, $phone);
    }

    /**
     * @inheritDoc
     */
    public function setHours($hours)
    {
        return $this->setData(self::FIELD_HOURS, $hours);
    }

    /**
     * @inheritDoc
     */
    public function setStreetLine1($street_line1)
    {
        return $this->setData(self::FIELD_STREET_LINE1, $street_line1);
    }

    /**
     * @inheritDoc
     */
    public function setStreetLine2($street_line2)
    {
        return $this->setData(self::FIELD_STREET_LINE2, $street_line2);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(self::FIELD_CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::FIELD_POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($region_id)
    {
        return $this->setData(self::FIELD_REGION_ID, $region_id);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($country_id)
    {
        return $this->setData(self::FIELD_COUNTRY_ID, $country_id);
    }

    /**
     * @inheritDoc
     */
    public function setVatNumber($vat_number)
    {
        return $this->setData(self::FIELD_VAT_NUMBER, $vat_number);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::FIELD_STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function setStoreCode($storeCode)
    {
        return $this->setData(self::FIELD_STORE_CODE, $storeCode);
    }
}
