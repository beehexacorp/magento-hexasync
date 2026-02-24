<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Beehexa\HexaSync\Api\Data;

interface LogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get log list.
     * @return \Beehexa\HexaSync\Api\Data\LogInterface[]
     */
    public function getItems();

    /**
     * Set message list.
     * @param \Beehexa\HexaSync\Api\Data\LogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

