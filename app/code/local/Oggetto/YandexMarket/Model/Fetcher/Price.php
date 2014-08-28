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
 * Price fetcher for products
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Model_Fetcher_Price
{
    /**
     * Get product prices from names
     *
     * @param array $names
     * @return array
     */
    public function getPricesFromNames(array $names)
    {
        /** @var Oggetto_YandexMarket_Model_Api $api */
        $api = Mage::getModel('yandex_market/api', Oggetto_YandexMarket_Model_Api::API_MODE_PARSING);

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');

        $prices = [];

        foreach ($names as $item) {
            try {
                $price = $api->getAvgPriceByTextSearch($item['name']);
            } catch (Oggetto_YandexMarket_Model_Exception_EmptyResult $e) {
                $price = null;
            } catch (Exception $e) {
                Mage::logException($e);
                $price = null;
            }

            if (!is_null($price)) {
                $product->load($item['entity_id']);

                if ($product->getFinalPrice() > $price) {
                    $price *= Mage::helper('yandex_market')->getRatioForGreaterPrice();
                }

                $price = $this->_convertPriceToBaseCurrency($price);
            }

            $prices[] = [
                'entity_id'     => $item['entity_id'],
                'value'         => $price
            ];
        }
        return $prices;
    }

    /**
     * Convert price to base currency
     *
     * @param string $price
     * @return float
     */
    protected function _convertPriceToBaseCurrency($price)
    {
        $currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $ymCurrency = Mage::getModel('yandex_market/api')->getYmCurrencyCode();

        return Mage::app()->getStore()->roundPrice(
            1 / Mage::helper('directory')->currencyConvert(1 / $price, $currentCurrency, $ymCurrency)
        );
    }
}
