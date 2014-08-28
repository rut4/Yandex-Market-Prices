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

/**
 * Price indexer resource
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Model_Resource_Indexer_Price extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setResource('yandex_market');
    }

    /**
     * Reindex entity(ies)
     *
     * @param int $productId
     * @return void
     */
    protected function _reindexEntity($productId = null)
    {
        $select = $this->_getWriteAdapter()->select();

        $conditions = ['product.entity_id = attribute_name.entity_id', 'attribute.attribute_id = attribute_name.attribute_id'];
        $select->from(['product' => $this->getTable('catalog/product')], ['entity_id'])
            ->join(['attribute' => $this->getTable('eav/attribute')], "attribute.attribute_code = 'name'", [])
            ->join(['attribute_name' => 'catalog_product_entity_varchar'], implode(' AND ', $conditions), ['name' => 'value']);

        if (!is_null($productId)) {
            if (!is_array($productId)) {
                $productId = [$productId];
            }
            $select->where('product.entity_id IN(?)', $productId);

            $this->_getIndexAdapter()->delete(
                $this->getTable('yandex_market/price'),
                ['entity_id IN(?)' => $productId]
            );
        } else {
            $this->_getIndexAdapter()->delete($this->getTable('yandex_market/price'));
        }

        $result = $this->_getIndexAdapter()->fetchAll($select);

        $prices = Mage::getModel('yandex_market/fetcher_price')->getPricesFromNames($result);

        $this->_getIndexAdapter()->insertMultiple($this->getTable('yandex_market/price'), $prices);
    }

    /**
     * Reindex all entities
     *
     * @return void
     */
    public function reindexAll()
    {
        $this->_reindexEntity();
    }

    /**
     * Reindex on product save
     *
     * @param Mage_Index_Model_Event $event
     * @return void
     */
    public function catalogProductSave($event)
    {
        $this->_reindexEntity($event->getData('product_id'));
    }

    /**
     * Reindex on products mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return void
     */
    public function catalogProductMassAction($event)
    {
        $this->_reindexEntity($event->getData('product_ids'));
    }
}
