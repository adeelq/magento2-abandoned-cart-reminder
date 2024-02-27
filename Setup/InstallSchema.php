<?php

namespace Adeelq\AbandonedCartReminder\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritDoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();
        $table = $setup->getTable('adeelq_abandoned_cart_reminder');

        if ($setup->getConnection()->isTableExists($table)) {
            $setup->getConnection()->dropTable($table);
        }

        $newTable = $setup->getConnection()->newTable($table);
        $newTable->addColumn(
            'id',
            Table::TYPE_INTEGER,
            10,
            ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
            'Primary Key'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'quote_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Quote Id'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Customer Id'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'email_sent',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Email Sent?'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            [],
            'Created At'
        );

        $newTable->addIndex($setup->getIdxName($table, ['store_id']), ['store_id'])
            ->addIndex($setup->getIdxName($table, ['quote_id']), ['quote_id'])
            ->addIndex($setup->getIdxName($table, ['customer_id']), ['customer_id'])
            ->addIndex($setup->getIdxName($table, ['email']), ['email'])
            ->addIndex($setup->getIdxName($table, ['email_sent']), ['email_sent'])
            ->addIndex($setup->getIdxName($table, ['created_at']), ['created_at']);

        $newTable->addForeignKey(
            $setup->getFkName(
                $table,
                'store_id',
                $setup->getTable('store'),
                'store_id'
            ),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $table,
                'quote_id',
                $setup->getTable('quote'),
                'entity_id'
            ),
            'quote_id',
            $setup->getTable('quote'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $newTable->setComment('AdeelQ Abandoned Cart entity table');
        $setup->getConnection()->createTable($newTable);
        $setup->endSetup();
    }
}
