<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Beehexa\HexaSync\Model;

use Beehexa\HexaSync\Api\Data\LogInterface;
use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel implements LogInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Beehexa\HexaSync\Model\ResourceModel\Log::class);
    }

    /**
     * @inheritDoc
     */
    public function getLogId()
    {
        return $this->getData(self::LOG_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLogId($logId)
    {
        return $this->setData(self::LOG_ID, $logId);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getProfileName()
    {
        return $this->getData(self::PROFILE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProfileName($profileName)
    {
        return $this->setData(self::PROFILE_NAME, $profileName);
    }

    /**
     * @inheritDoc
     */
    public function getActionType()
    {
        return $this->getData(self::ACTION_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setActionType($actionType)
    {
        return $this->setData(self::ACTION_TYPE, $actionType);
    }

    /**
     * @inheritDoc
     */
    public function getHexasyncId()
    {
        return $this->getData(self::HEXASYNC_ID);
    }

    /**
     * @inheritDoc
     */
    public function setHexasyncId($hexasyncId)
    {
        return $this->setData(self::HEXASYNC_ID, $hexasyncId);
    }

    /**
     * @inheritDoc
     */
    public function getReferenceInfo()
    {
        return $this->getData(self::REFERENCE_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setReferenceInfo($referenceInfo)
    {
        return $this->setData(self::REFERENCE_INFO, $referenceInfo);
    }

    /**
     * @inheritDoc
     */
    public function getError()
    {
        return $this->getData(self::ERROR);
    }

    /**
     * @inheritDoc
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * @inheritDoc
     */
    public function getTaskId()
    {
        return $this->getData(self::TASK_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTaskId($taskId)
    {
        return $this->setData(self::TASK_ID, $taskId);
    }

    /**
     * @inheritDoc
     */
    public function getTaskStatus()
    {
        return $this->getData(self::TASK_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setTaskStatus($taskStatus)
    {
        return $this->setData(self::TASK_STATUS, $taskStatus);
    }

    /**
     * @inheritDoc
     */
    public function getExecutedAt()
    {
        return $this->getData(self::EXECUTED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setExecutedAt($executedAt)
    {
        return $this->setData(self::EXECUTED_AT, $executedAt);
    }

    /**
     * @inheritDoc
     */
    public function getExecuteAt()
    {
        return $this->getData(self::EXECUTE_AT);
    }

    /**
     * @inheritDoc
     */
    public function setExecuteAt($executeAt)
    {
        return $this->setData(self::EXECUTE_AT, $executeAt);
    }

    /**
     * @inheritDoc
     */
    public function getRetryCount()
    {
        return $this->getData(self::RETRY_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setRetryCount($retryCount)
    {
        return $this->setData(self::RETRY_COUNT, $retryCount);
    }
}

