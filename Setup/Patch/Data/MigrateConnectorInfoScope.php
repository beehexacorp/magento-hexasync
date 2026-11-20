<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Beehexa\HexaSync\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Moves connector information saved by earlier versions to the scope Magento reads
 *
 * Earlier versions saved "beehexa/connector/*" with scope "store". Magento only loads "stores", so the registered
 * information never showed up in the admin.
 */
class MigrateConnectorInfoScope implements DataPatchInterface
{
    private const PATH_PATTERN = 'beehexa/connector/%';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $table = $this->moduleDataSetup->getTable('core_config_data');

        $select = $connection->select()
            ->from($table, ['scope_id', 'path', 'value'])
            ->where('scope = ?', 'store')
            ->where('path LIKE ?', self::PATH_PATTERN);

        foreach ($connection->fetchAll($select) as $row) {
            $exists = $connection->fetchOne(
                $connection->select()
                    ->from($table, ['config_id'])
                    ->where('scope = ?', 'stores')
                    ->where('scope_id = ?', (int)$row['scope_id'])
                    ->where('path = ?', $row['path'])
            );
            if (!$exists) {
                $connection->insert($table, [
                    'scope' => 'stores',
                    'scope_id' => (int)$row['scope_id'],
                    'path' => $row['path'],
                    'value' => $row['value'],
                ]);
            }
        }

        $connection->delete($table, ['scope = ?' => 'store', 'path LIKE ?' => self::PATH_PATTERN]);

        return $this;
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
     */
    public function getAliases(): array
    {
        return [];
    }
}
