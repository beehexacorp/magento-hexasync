<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Beehexa\HexaSync\Api\Data;

interface LogInterface
{

    const EXECUTED_AT = 'executed_at';
    const ERROR = 'error';
    const PROFILE_NAME = 'profile_name';
    const ACTION_TYPE = 'action_type';
    const CREATED_AT = 'created_at';
    const MESSAGE = 'message';
    const TASK_STATUS = 'task_status';
    const TASK_ID = 'task_id';
    const REFERENCE_INFO = 'reference_info';
    const LOG_ID = 'log_id';
    const HEXASYNC_ID = 'hexasync_id';
    const RETRY_COUNT = 'retry_count';
    const EXECUTE_AT = 'execute_at';

    /**
     * Get log_id
     * @return string|null
     */
    public function getLogId();

    /**
     * Set log_id
     * @param string $logId
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setLogId($logId);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setMessage($message);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get profile_name
     * @return string|null
     */
    public function getProfileName();

    /**
     * Set profile_name
     * @param string $profileName
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setProfileName($profileName);

    /**
     * Get action_type
     * @return string|null
     */
    public function getActionType();

    /**
     * Set action_type
     * @param string $actionType
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setActionType($actionType);

    /**
     * Get hexasync_id
     * @return string|null
     */
    public function getHexasyncId();

    /**
     * Set hexasync_id
     * @param string $hexasyncId
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setHexasyncId($hexasyncId);

    /**
     * Get reference_info
     * @return string|null
     */
    public function getReferenceInfo();

    /**
     * Set reference_info
     * @param string $referenceInfo
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setReferenceInfo($referenceInfo);

    /**
     * Get error
     * @return string|null
     */
    public function getError();

    /**
     * Set error
     * @param string $error
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setError($error);

    /**
     * Get task_id
     * @return string|null
     */
    public function getTaskId();

    /**
     * Set task_id
     * @param string $taskId
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setTaskId($taskId);

    /**
     * Get task_status
     * @return string|null
     */
    public function getTaskStatus();

    /**
     * Set task_status
     * @param string $taskStatus
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setTaskStatus($taskStatus);

    /**
     * Get executed_at
     * @return string|null
     */
    public function getExecutedAt();

    /**
     * Set executed_at
     * @param string $executedAt
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setExecutedAt($executedAt);

    /**
     * Get execute_at
     * @return string|null
     */
    public function getExecuteAt();

    /**
     * Set execute_at
     * @param string $executeAt
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setExecuteAt($executeAt);

    /**
     * Get retry_count
     * @return string|null
     */
    public function getRetryCount();

    /**
     * Set retry_count
     * @param string $retryCount
     * @return \Beehexa\HexaSync\Log\Api\Data\LogInterface
     */
    public function setRetryCount($retryCount);
}

