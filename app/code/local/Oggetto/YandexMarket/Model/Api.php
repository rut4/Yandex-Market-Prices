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
 * Yandex Market API model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Model_Api
{
    const DATA_FORMAT_JSON  = '.json';
    const DATA_FORMAT_XML   = '.xml';

    const API_MODE_PARSING  = 0;
    const API_MODE_REST     = 1;

    protected $_apiMode;

    protected $_parsingUrl = 'http://m.market.yandex.ru/';
    protected $_restApiUrl = 'https://api.content.market.yandex.ru/v1/';

    protected $_textSearchApiSuffix = 'search';

    protected $_httpClient;

    /**
     * Class constructor
     *
     * @param int $mode Api mode
     */
    public function __construct($mode = self::API_MODE_PARSING)
    {
        $this->_apiMode = $mode;
    }

    /**
     * Get HTTP client
     *
     * @param string $url Url
     * @return Zend_Http_Client
     */
    protected function _getClient($url)
    {
        if (is_null($this->_httpClient)) {
            $this->_httpClient = new Varien_Http_Client();
        }
        return $this->_httpClient->resetParameters(true)->setUri($url);
    }

    /**
     * Make HTTP request with specified url and parameters
     *
     * @param string $url    Url
     * @param array  $params Parameters
     * @param bool   $auth   Should use authorization with key
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _makeRequest($url, $params, $auth = true)
    {
        $response = null;

        try {
            $client = $this->_getClient($url);
            $client->setParameterGet($params);
            if ($auth) {
                $client->setHeaders('Authorization', Mage::helper('yandex_market')->getAuthorizationKey());
            }
            $response = $client->request(Varien_Http_Client::GET);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $body = null;
        if (!is_null($response)) {
            if ($response->getStatus() == 200) {
                if ($this->_apiMode == self::API_MODE_REST) {
                    $body = Mage::helper('core')->jsonDecode($response->getBody());
                } else {
                    $body = $response->getBody();
                }
            } else if ($response->getStatus() == 401) {
                Mage::throwException(Mage::helper('yandex_market')->__('Authorization failed.'));
            } else {
                Mage::throwException(Mage::helper('yandex_market')->__('Servers responded with status %s',
                    $response->getStatus()));
            }
        }
        if (!is_null($body)) {
            return $body;
        }
        Mage::throwException(Mage::helper('yandex_market')->__('Connection error.'));
    }

    /**
     * Get price for product by text search
     *
     * @param string $text Search text
     * @return string
     * @throws Oggetto_YandexMarket_Model_Exception_EmptyResult|Mage_Core_Exception
     */
    public function getAvgPriceByTextSearch($text)
    {
        $url = $this->_apiMode == self::API_MODE_REST ?
            $this->_restApiUrl . $this->_textSearchApiSuffix . self::DATA_FORMAT_JSON :
            $this->_parsingUrl . $this->_textSearchApiSuffix . self::DATA_FORMAT_XML;

        $auth = $this->_apiMode == self::API_MODE_REST;

        $responseResult = $this->_makeRequest($url, ['text' => $text], $auth);

        if (empty($responseResult)) {
            Mage::throwException(Mage::helper('yandex_market')->__('Empty api result.'));
        }

        if ($this->_apiMode == self::API_MODE_REST) {
            return $this->_getPriceFormRestApiResult($responseResult);
        } elseif ($this->_apiMode == self::API_MODE_PARSING) {
            /** @var Oggetto_YandexMarket_Model_Parser $parser */
            $parser = Mage::getModel('yandex_market/parser');

            $itemLink = $parser->parseFirstItemLink($responseResult);

            if (is_null($itemLink)) {
                throw new Oggetto_YandexMarket_Model_Exception_EmptyResult('Empty api result');
            }

            $result = $this->_makeRequest($itemLink, [], false);
            return $parser->parsePrice($result);
        }
    }

    /**
     * Get YM currency code
     *
     * @return string
     */
    public function getYmCurrencyCode()
    {
        return 'RUB';
    }

    /**
     * Get price from rest api result
     *
     * @param array $result Response result
     * @return string
     * @throws Oggetto_YandexMarket_Model_Exception_EmptyResult
     */
    protected function _getPriceFormRestApiResult($result)
    {
        if (isset($result['searchResult']) && isset($result['searchResult']['results'])) {
            $searchResults = $result['searchResult']['results'];

            foreach ($searchResults as $_type => $_info) {
                if ($_type == 'model') {
                    return $_info['prices']['avg'];
                }
            }

            throw new Oggetto_YandexMarket_Model_Exception_EmptyResult(
                Mage::helper('yandex_market')->__("Query didn't return any prices.")
            );
        }
    }
}
