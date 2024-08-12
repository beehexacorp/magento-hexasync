<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Beehexa\HexaSync\Setup\Patch\Data;

use Beehexa\HexaSync\Model\HexaSyncIntegrationManagement;
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
    protected HexaSyncIntegrationManagement $integrationManager;

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
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     *
     * @throws LocalizedException
     */
    public function apply(): \Magento\Framework\Setup\Patch\PatchInterface|AddingIntegrationUser|static
    {
        $this->integrationManager->generateIntegration();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
