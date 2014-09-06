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
 * Data helper test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test helper returns authorization key
     *
     * @return void
     */
    public function testReturnsAuthorizationKey()
    {
        $this->assertEquals(
            Mage::getStoreConfig('yandex_market_price/general/auth_key'),
            Mage::helper('yandex_market')->getAuthorizationKey()
        );
    }

    /**
     * Test helper returns ratio for greater price then YM
     *
     * @return void
     */
    public function testReturnsRatioForGreaterPrice()
    {
        $this->assertEquals(
            Mage::getStoreConfig('yandex_market_price/general/ratio_greater_price'),
            Mage::helper('yandex_market')->getRatioForGreaterPrice()
        );
    }

    /**
     * Test converts price to base currency
     *
     * @param int $price Price
     * @dataProvider dataProvider
     * @return void
     */
    public function testConvertsPriceToBaseCurrency($price)
    {
        $api = $this->getModelMock('yandex_market/api', ['getYmCurrencyCode']);

        $api->expects($this->once())
            ->method('getYmCurrencyCode')
            ->will($this->returnValue('RUB'));

        $this->replaceByMock('model', 'yandex_market/api', $api);

        $directoryHelper = $this->getHelperMock('directory/data', ['currencyConvert']);

        $directoryHelper->expects($this->once())
            ->method('currencyConvert')
            ->with(
                $this->equalTo(1 / $price),
                $this->equalTo('USD'),
                $this->equalTo('RUB')
            )
            ->will($this->returnValue(1 / $price));

        $this->replaceByMock('helper', 'directory', $directoryHelper);

        $helper = Mage::helper('yandex_market/data');

        $this->assertEquals($price, $helper->convertPriceToBaseCurrency($price));
    }
}
