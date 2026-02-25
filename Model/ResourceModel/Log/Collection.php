<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Beehexa\HexaSync\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'log_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Beehexa\HexaSync\Model\Log::class,
            \Beehexa\HexaSync\Model\ResourceModel\Log::class
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setOrder($this->_idFieldName,'DESC');
        return $this;
    }
}

