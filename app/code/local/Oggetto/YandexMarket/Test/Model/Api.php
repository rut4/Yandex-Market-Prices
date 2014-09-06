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
 * Api model test
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Test_Model_Api extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test API returns average price by text search with rest API mode and success response from server
     *
     * @param string $text  Text to search
     * @param float  $price Price
     * @dataProvider dataProvider
     * @return void
     */
    public function testReturnsAvgPriceByTextSearchWithRestApiModeAndSuccessResponse($text, $price)
    {
        $httpClientMock = $this->getMock('Varien_Http_Client', ['setParameterGet', 'setHeaders', 'request']);
        $httpResponseMock = $this->getMock('Zend_Http_Response', ['getStatus', 'getBody'], [200, []]);

        $httpResponseMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(200));

        $httpResponseMock->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode([
                'searchResult' => [
                    'results' => [
                        'model' => [
                            'prices' => [
                                'avg' => $price
                            ]
                        ]
                    ]
                ]
            ])));

        $httpClientMock->expects($this->once())
            ->method('setParameterGet')
            ->with([
                'text' => $text
            ]);

        $httpClientMock->expects($this->once())
            ->method('setHeaders')
            ->with(
                $this->equalTo('Authorization'),
                $this->equalTo(Mage::helper('yandex_market')->getAuthorizationKey())
            );

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('GET'))
            ->will($this->returnValue($httpResponseMock));

        $apiMock = $this->getModelMock(
            'yandex_market/api',
            ['_getClient'],
            false,
            [Oggetto_YandexMarket_Model_Api::API_MODE_REST]
        );

        $apiMock->expects($this->once())
            ->method('_getClient')
            ->with($this->anything())
            ->will($this->returnValue($httpClientMock));

        $this->assertEquals($price, $apiMock->getAvgPriceByTextSearch($text));
    }

    /**
     * Test API returns average price by text search with parsing API mode and success response from server
     *
     * @param string $text  Text to search
     * @param float  $price Price
     * @param string $body  Body
     * @dataProvider dataProvider
     * @return void
     */
    public function testReturnsAvgPriceByTextSearchWithParsingApiModeAndSuccessResponse($text, $price, $body)
    {
        $httpClientMock = $this->getMock('Varien_Http_Client', ['setParameterGet', 'request']);
        $httpResponseMock = $this->getMock('Zend_Http_Response', ['getStatus', 'getBody'], [200, []]);

        $httpResponseMock->expects($this->exactly(2))
            ->method('getStatus')
            ->will($this->returnValue(200));

        $httpResponseMock->expects($this->exactly(2))
            ->method('getBody')
            ->will($this->returnValue($body));

        $httpClientMock->expects($this->exactly(2))
            ->method('setParameterGet')
            ->with($this->anything());

        $httpClientMock->expects($this->exactly(2))
            ->method('request')
            ->with($this->equalTo(Varien_Http_Client::GET))
            ->will($this->returnValue($httpResponseMock));

        $parserMock = $this->getModelMock('yandex_market/parser', ['parseFirstItemLink', 'parsePrice']);

        $parserMock->expects($this->once())
            ->method('parseFirstItemLink')
            ->will($this->returnValue($body));

        $parserMock->expects($this->once())
            ->method('parsePrice')
            ->with($this->equalTo($body))
            ->will($this->returnValue($price));

        $this->replaceByMock('model', 'yandex_market/parser', $parserMock);

        $apiMock = $this->getModelMock(
            'yandex_market/api',
            ['_getClient'],
            false,
            [Oggetto_YandexMarket_Model_Api::API_MODE_PARSING]
        );

        $apiMock->expects($this->exactly(2))
            ->method('_getClient')
            ->with($this->anything())
            ->will($this->returnValue($httpClientMock));

        $this->assertEquals($price, $apiMock->getAvgPriceByTextSearch($text));
    }

    /**
     * Test api throws exception when response code doesn't equal 200
     *
     * @param int $text Text to search
     * @param int $mode Api mode
     * @param int $code Response code
     * @dataProvider dataProvider
     * @expectedException Mage_Core_Exception
     * @return void
     */
    public function testThrowsExceptionWhenResponseCodeDoesNotEqual200($text, $mode, $code)
    {
        $httpClientMock = $this->getMock('Varien_Http_Client', ['setParameterGet', 'setHeaders', 'request']);
        $httpResponseMock = $this->getMock('Zend_Http_Response', ['getStatus'], [$code, []]);

        $httpResponseMock->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($code));

        $httpClientMock->expects($this->once())
            ->method('setParameterGet')
            ->with([
                'text' => $text
            ]);

        if ($mode == Oggetto_YandexMarket_Model_Api::API_MODE_REST) {
            $httpClientMock->expects($this->once())
                ->method('setHeaders')
                ->with(
                    $this->equalTo('Authorization'),
                    $this->equalTo(Mage::helper('yandex_market')->getAuthorizationKey())
                );
        }

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('GET'))
            ->will($this->returnValue($httpResponseMock));

        $apiMock = $this->getModelMock(
            'yandex_market/api',
            ['_getClient'],
            false,
            [$mode]
        );

        $apiMock->expects($this->once())
            ->method('_getClient')
            ->with($this->anything())
            ->will($this->returnValue($httpClientMock));

        $apiMock->getAvgPriceByTextSearch($text);
    }
}
