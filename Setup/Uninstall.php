<?php

namespace Adeelq\AbandonedCartReminder\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @inheritDoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();
        $table = $setup->getTable('adeelq_abandoned_cart_reminder');
        if ($setup->getConnection()->isTableExists($table)) {
            $setup->getConnection()->dropTable($table);
        }
        $setup->endSetup();
    }
}
