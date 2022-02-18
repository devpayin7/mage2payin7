<?php

namespace Payin7\Mage2Payin7\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface {

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		if (version_compare($context->getVersion(), '2.2.2', '<')) {
            $setup->startSetup();
            $conn = $setup->getConnection();
            //commission
            $tableName = $setup->getTable('mage2payin7_commission');
            if($conn->isTableExists($tableName)) {
                $conn->truncateTable($tableName);
            }
		}

        if (version_compare($context->getVersion(), '2.2.2', '<')) {
            $setup->startSetup();
            $conn = $setup->getConnection();
            //commission
            $tableName = $setup->getTable('mage2payin7_cache');
            if($conn->isTableExists($tableName)) {
                $conn->truncateTable($tableName);
                $data = [
                    'id' => 0,
                    'timestamp' => time()
                ];
                $conn->insert('mage2payin7_cache',$data);
            }
        }
	}
}
