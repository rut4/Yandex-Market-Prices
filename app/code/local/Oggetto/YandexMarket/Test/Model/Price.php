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
 * Price model test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Model_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Setup session before test
     *
     * @return void
     */
    public function setUp()
    {
        $session = $this->getModelMock('core/session', ['start']);
        $this->replaceByMock('singleton', 'core/session', $session);
    }

    /**
     * Test price is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_YandexMarket_Model_Price', Mage::getModel('yandex_market/price'));
    }

    /**
     * Test price loads self by product id
     *
     * @param int $productId Product id
     * @dataProvider dataProvider
     * @return void
     */
    public function testLoadsItselfByProductId($productId)
    {
        $priceMock = $this->getModelMock('yandex_market/price', ['load']);

        $priceMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($productId));

        $priceMock->loadByProductId($productId);
    }

    /**
     * Test price returns formatted price
     *
     * @param int    $callNumber Number of test calls
     * @param string $price      Product price
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsFormattedPrice($callNumber, $price)
    {
        $priceMock = $this->getModelMock('yandex_market/price', ['getValue']);

        $priceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($price));

        $this->assertEquals($this->expected($callNumber)->getFormatted(), $priceMock->getFormattedPrice());
    }

    /**
     * Test price returns has value
     *
     * @param int  $callNumber Number of test calls
     * @param bool $result     Has value result
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsIsExistsPrice($callNumber, $result)
    {
        $price = $this->getModelMock('yandex_market/price', ['hasValue']);

        $price->expects($this->once())
            ->method('hasValue')
            ->will($this->returnValue($result));

        $this->assertEquals($this->expected($callNumber)->getIsExistPrice(), $price->isExistPrice());
    }
}
