<?php


namespace Payin7\Mage2Payin7\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{

    /**
     * @inheritDoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.2.2', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('mage2payin7_commission'),
                'timestamp',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'comment' => 'Timestamp'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.2', '<')) {
            $tableName = $installer->getTable('mage2payin7_cache');
            if($installer->getConnection()->isTableExists($tableName) != true){
                $table = $installer->getConnection()->newTable($tableName)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        '11',
                        ['unsigned'=>false,'nullable'=>false,'primary'=>true]
                    )
                    ->addColumn(
                        'timestamp',
                        Table::TYPE_INTEGER,
                        '11',
                        ['unsigned'=>false,'nullable'=>true,'primary'=>false]
                    )
                    ->setOption('charset','utf8');
                $installer->getConnection()->createTable($table);
            }
        }
        $installer->endSetup();
    }
}