<?php 
namespace Payin7\Mage2Payin7\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
        $setup->startSetup();
        $conn = $setup->getConnection();
        //commission
        $tableName = $setup->getTable('mage2payin7_commission');
        if($conn->isTableExists($tableName) != true){
            $table = $conn->newTable($tableName)
                            ->addColumn(
                                'amount',
                                Table::TYPE_DECIMAL,
                                '12,4',
                                ['unsigned'=>true,'nullable'=>false,'primary'=>true]
                                )
                            ->addColumn(
                                'commission',
                                Table::TYPE_BLOB,
                                null,
                                ['nullable'=>true]
                                )
                            ->setOption('charset','utf8');
            $conn->createTable($table);
        }
        //quote
        $tableName = $setup->getTable('mage2payin7_savedQuote');
        if($conn->isTableExists($tableName) != true){
            $table = $conn->newTable($tableName)
                            ->addColumn(
                                'quote_id',
                                Table::TYPE_INTEGER,
                                null,
                                ['unsigned'=>true,'nullable'=>false,'primary'=>true, 'auto_increment'=>false]
                                )
                            ->addColumn(
                                'paid_email',
                                Table::TYPE_BOOLEAN,
                                null,
                                ['nullable'=>false,'default'=>0]
                                )
                            ->addColumn(
                                'creating_order',
                                Table::TYPE_BOOLEAN,
                                null,
                                ['nullable'=>false,'default'=>0]
                                )
                            ->addColumn(
                                'temp_id',
                                Table::TYPE_TEXT,
                                100,
                                ['nullable'=>false,'default'=>'']
                                )
                            ->setOption('charset','utf8');
            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}