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
 * Price fetcher model test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Model_Fetcher_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Setup session mock
     *
     * @return void
     */
    public function setUp()
    {
        $session = $this->getModelMock('core/session', ['start']);
        $this->replaceByMock('model', 'core/session', $session);
    }
    /**
     * Test fetcher returns prices for names
     *
     * @param array $ids    Product ids
     * @param array $names  Product names
     * @param array $prices Product prices
     * @dataProvider dataProvider
     * @return void
     */
    public function testReturnsPricesForNames(array $ids, array $names, array $prices)
    {
        $items = array_map(function ($id, $name) {
            return [
                'entity_id' => $id,
                'name'      => $name
            ];
        }, $ids, $names);

        $apiMock = $this->getModelMock('yandex_market/api', ['getAvgPriceByTextSearch'], false, [1]);

        $productMock = $this->getModelMock('catalog/product', ['load', 'getFinalPrice']);

        $helperMock = $this->getHelperMock('yandex_market/data', ['convertPriceToBaseCurrency']);

        $expected = [];
        for ($i = 0; $i < count($names); $i++) {
            $apiMock->expects($this->at($i))
                ->method('getAvgPriceByTextSearch')
                ->with($this->equalTo($names[$i]))
                ->will($this->returnValue($prices[$i]));

            $productMock->expects($this->at($i))
                ->method('load')
                ->with($this->equalTo($ids[$i]));

            $productMock->expects($this->at($i))
                ->method('getFinalPrice')
                ->will($this->returnValue($prices[$i] - 10));

            $helperMock->expects($this->at($i))
                ->method('convertPriceToBaseCurrency')
                ->will($this->returnArgument(0));

            $expected[] = [
                'entity_id' => $ids[$i],
                'value'     => $prices[$i]
            ];
        }
        $this->replaceByMock('model', 'yandex_market/api', $apiMock);
        $this->replaceByMock('model', 'catalog/product', $productMock);
        $this->replaceByMock('helper', 'yandex_market', $helperMock);

        $this->assertEquals($expected, Mage::getModel('yandex_market/fetcher_price')->getPricesForNames($items));
    }

}
