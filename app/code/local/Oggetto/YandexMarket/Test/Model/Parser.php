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
     * Test parser detects first item link
     *
     * @param string $doc  Document
     * @param string $link Link of the first item
     * @dataProvider dataProvider
     * @return void
     */
    public function testParsesFirstItemLink($doc, $link)
    {
        $parser = Mage::getModel('yandex_market/parser');
        $this->assertEquals($link, $parser->parseFirstItemLink($doc));
    }

    /**
     * Test parser detects first item link
     *
     * @param string $doc   Document
     * @param string $price Price of the first item
     * @dataProvider dataProvider
     * @return void
     */
    public function testParsesFirstItemPrice($doc, $price)
    {
        $parser = Mage::getModel('yandex_market/parser');
        $this->assertEquals($price, $parser->parsePrice($doc));
    }
}
