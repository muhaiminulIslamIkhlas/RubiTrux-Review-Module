<?php

namespace Harriswebworks\feeforeview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('feefo_review_data')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Item ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            255,
            ['nullable' => false],
            'Product Id'
        )->addColumn(
            'rating_value',
            Table::TYPE_INTEGER,
            255,
            ['nullable' => false],
            'Rating Value'
        )->addColumn(
            'review_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Review Id'
        )->addColumn(
            'review',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Review'
        )->addColumn(
            'reviews_url',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Reviews Url'
        )->addColumn(
            'customer_display_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Display Name'
        )->addColumn(
            'created_at',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Created at'
        )->addColumn(
            'last_updated_date',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Last Updated Date'
        )->addColumn(
            'additional_param',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Additional Param'
        )->setComment(
            'Sample Items'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
