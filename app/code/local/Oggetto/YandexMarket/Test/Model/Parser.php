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
 * Parser model test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Model_Parser extends EcomDev_PHPUnit_Test_Case
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
     * Test parser is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_YandexMarket_Model_Parser', Mage::getModel('yandex_market/parser'));
    }


    /**
     * Test parser detects first item link
     *
     * @param int    $callNumber Number of test calls
     * @param string $doc        Document
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testParsesFirstItemLink($callNumber, $doc)
    {
        $this->assertEquals(
            $this->expected($callNumber)->getFirstItemLink(),
            Mage::getModel('yandex_market/parser')->parseFirstItemLink($doc)
        );
    }

    /**
     * Test parser detects first item link
     *
     * @param int    $callNumber Number of test calls
     * @param string $doc        Document
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testParsesFirstItemPrice($callNumber, $doc)
    {
        $this->assertEquals(
            $this->expected($callNumber)->getPrice(),
            Mage::getModel('yandex_market/parser')->parsePrice($doc)
        );
    }
}
