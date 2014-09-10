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
 * Price resource model test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Model_Resource_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test price model resource is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf(
            'Oggetto_YandexMarket_Model_Resource_Price',
            Mage::getResourceModel('yandex_market/price')
        );
    }

    /**
     * Test price model initializations with main table
     *
     * @return void
     */
    public function testInitialisationsWithMainTable()
    {
        $this->assertEquals(
            'catalog_product_index_price_yandex_market',
            Mage::getResourceModel('yandex_market/price')->getMainTable()
        );
    }

    /**
     * Test price model initializations with id field name
     *
     * @return void
     */
    public function testInitialisationsWithIdFieldName()
    {
        $this->assertEquals(
            'entity_id',
            Mage::getResourceModel('yandex_market/price')->getIdFieldName()
        );
    }
}
