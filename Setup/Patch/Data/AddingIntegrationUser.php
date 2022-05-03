<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Beehexa\HexaSync\Setup\Patch\Data;

use \Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Activate data collection mechanism
 */
class AddingIntegrationUser implements DataPatchInterface
{
    /**
     * @var HexaSyncIntegrationManagement
     */
    protected $integrationManager;

    /**
     * @param HexaSyncIntegrationManagement $integrationManager
     */
    public function __construct(
        HexaSyncIntegrationManagement $integrationManager
    ) {
        $this->integrationManager = $integrationManager;
    }

    /**
     * @inheritDoc
     *
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->integrationManager->generateToken();
        $this->integrationManager->activateIntegration();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
