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
 * Price indexer resource test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Model_Resource_Indexer_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test price indexer available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf(
            'Oggetto_YandexMarket_Model_Resource_Indexer_Price',
            Mage::getResourceModel('yandex_market/indexer_price')
        );
    }

    /**
     * Test price indexer initializations with main table
     *
     * @return void
     */
    public function testInitialisationsWithMainTable()
    {
        $this->assertEquals(
            'catalog_product_index_price_yandex_market',
            Mage::getResourceModel('yandex_market/indexer_price')->getMainTable()
        );
    }

    /**
     * Test price indexer initializations with id field name
     *
     * @return void
     */
    public function testInitialisationsWithIdFieldName()
    {
        $this->assertEquals(
            'entity_id',
            Mage::getResourceModel('yandex_market/indexer_price')->getIdFieldName()
        );
    }

    /**
     * Test price indexer reindexes products
     *
     * @param int       $callNumber Number of method calls
     * @param array     $items      Product items
     * @param array     $prices     Product prices
     * @param int|array $productId  Product ID
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReindexesProducts($callNumber, array $items, array $prices, $productId = null)
    {
        $select = $this->getMock('Varien_Db_Select', ['from', 'join', 'where'], [], '', false);

        $select->expects($this->once())
            ->method('from')
            ->with(
                $this->equalTo(['product' => 'catalog_product_entity']),
                $this->equalTo(['entity_id'])
            )
            ->will($this->returnSelf());

        $select->expects($this->at(1))
            ->method('join')
            ->with(
                $this->equalTo(['attribute' => 'eav_attribute']),
                $this->equalTo("attribute.attribute_code = 'name'"),
                $this->equalTo([])
            )
            ->will($this->returnSelf());

        $select->expects($this->at(2))
            ->method('join')
            ->with(
                $this->equalTo(['attribute_name' => 'catalog_product_entity_varchar']),
                $this->equalTo('product.entity_id = attribute_name.entity_id AND '
                    . 'attribute.attribute_id = attribute_name.attribute_id'),
                $this->equalTo(['name' => 'value'])
            )
            ->will($this->returnSelf());

        $wherePId = is_array($productId) ? $productId : [$productId];

        $select->expects($this->any())
            ->method('where')
            ->with(
                $this->equalTo('product.entity_id IN(?)'),
                $this->equalTo($wherePId)
            )
            ->will($this->returnSelf());

        $writeAdapter = $this->getMock('Varien_Object', ['select']);

        $writeAdapter->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));

        $indexAdapter = $this->getMock('Varien_Object', ['delete', 'insertMultiple', 'fetchAll']);

        $indexAdapter->expects($this->any())
            ->method('delete')
            ->with($this->anything())
            ->will($this->returnSelf());

        $indexAdapter->expects($this->any())
            ->method('insertMultiple')
            ->with(
                $this->equalTo(Mage::getResourceModel('yandex_market/price')->getMainTable()),
                $this->equalTo($this->expected($callNumber)->getPrices())
            )
            ->will($this->returnSelf());

        $indexAdapter->expects($this->any())
            ->method('fetchAll')
            ->with($this->equalTo($select))
            ->will($this->returnValue($items));

        $priceFetcher = $this->getModelMock('yandex_market/fetcher_price', ['getPricesForNames']);

        $priceFetcher->expects($this->once())
            ->method('getPricesForNames')
            ->with($this->equalTo($items))
            ->will($this->returnValue($prices));

        $this->replaceByMock('model', 'yandex_market/fetcher_price', $priceFetcher);

        $event = $this->getModelMock('index/event', ['getData']);

        $event->expects($this->any())
            ->method('getData')
            ->with($this->anything())
            ->will($this->returnValue($productId));

        $indexer = $this->getResourceModelMock('yandex_market/indexer_price', ['_getWriteAdapter', '_getIndexAdapter']);

        $indexer->expects($this->once())
            ->method('_getWriteAdapter')
            ->will($this->returnValue($writeAdapter));

        $indexer->expects($this->any())
            ->method('_getIndexAdapter')
            ->will($this->returnValue($indexAdapter));


        if (is_null($productId)) {
            $indexer->reindexAll();
        } elseif (is_array($productId)) {
            $indexer->catalogProductMassAction($event);
        } else {
            $indexer->catalogProductSave($event);
        }


    }
}
