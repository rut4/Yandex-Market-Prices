<?php
/**
 * Oggetto Web yandex market extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Yandex Market module to newer versions in the future.
 * If you wish to customize the Oggetto Yandex Market module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

try {
    /** @var Mage_Catalog_Model_Resource_Setup $this */
    $installer = $this;
    $installer->startSetup();

    $connection = $this->getConnection();

    if ($connection->isTableExists($this->getTable('yandex_market/price'))) {
        $connection->dropTable($this->getTable('yandex_market/price'));
    }

    $table = $connection
        ->newTable($this->getTable('yandex_market/price'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false
        ], 'Product Id')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
            'unsigned'  => true,
            'nullable'  => true
        ], 'Value')
        ->addForeignKey(
            $installer->getFkName('yandex_market/price', 'entity_id', 'catalog/product', 'entity_id'),
            'entity_id',
            $installer->getTable('catalog/product'),
            'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addIndex('entity_id', 'entity_id')
        ->setComment('Yandex Market Prices');

    $connection->createTable($table);

    $installer->endSetup();
} catch (Exception $e) {
    Mage::logException($e);
}
