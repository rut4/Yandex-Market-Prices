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
     * Test price fetcher model is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf(
            'Oggetto_YandexMarket_Model_Fetcher_Price',
            Mage::getModel('yandex_market/fetcher_price')
        );
    }

    /**
     * Test fetcher returns prices for names
     *
     * @param int   $callNumber Number of test calls
     * @param array $items      Products
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsPricesForNames($callNumber, $items)
    {
        $priceFetcher = $this->getModelMock('yandex_market/fetcher_price', ['getItemPrice']);

        $priceFetcher->expects($this->exactly(count($items)))
            ->method('getItemPrice')
            ->with($this->anything())
            ->will($this->returnValue([
                'entity_id' => 707,
                'value'     => 42
            ]));

        $this->assertEquals(
            $this->expected($callNumber)->getPrices(),
            $priceFetcher->getPricesForNames($items)
        );
    }

    /**
     * Test fetcher returns price for one item
     *
     * @param int   $callNumber   Number of test calls
     * @param array $item         Product info
     * @param float $productPrice Product price
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsPriceForOneItem($callNumber, $item, $productPrice)
    {
        $expectedPrice = $this->expected($callNumber)->getPrice()['value'];

        $api = $this->getModelMock('yandex_market/api', ['getAvgPriceByTextSearch'], false, [
            Oggetto_YandexMarket_Model_Api::API_MODE_PARSING
        ]);

        $api->expects($this->once())
            ->method('getAvgPriceByTextSearch')
            ->with($this->equalTo($item['name']))
            ->will($this->returnValue($expectedPrice));

        $this->replaceByMock('model', 'yandex_market/api', $api);

        $product = $this->getModelMock('catalog/product', ['load', 'getFinalPrice']);

        $product->expects($this->once())
            ->method('load')
            ->with($this->equalTo($item['entity_id']))
            ->will($this->returnSelf());

        $product->expects($this->once())
            ->method('getFinalPrice')
            ->will($this->returnValue($productPrice));

        $this->replaceByMock('model', 'catalog/product', $product);

        $helper = $this->getHelperMock('yandex_market/data', ['convertPriceToBaseCurrency', 'getRatioForGreaterPrice']);

        $helper->expects($this->once())
            ->method('convertPriceToBaseCurrency')
            ->with($this->equalTo($expectedPrice))
            ->will($this->returnValue($expectedPrice));

        $helper->expects($this->any())
            ->method('getRatioForGreaterPrice')
            ->will($this->returnValue(1));

        $this->replaceByMock('helper', 'yandex_market', $helper);

        $this->assertEquals(
            $this->expected($callNumber)->getPrice(),
            Mage::getModel('yandex_market/fetcher_price')->getItemPrice($item)
        );
    }
}
