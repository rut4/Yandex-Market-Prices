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
 * Yandex market price model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Model_Price extends Mage_Catalog_Model_Abstract
{

    /**
     * Initialization with resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('yandex_market/price');
    }

    /**
     * Load YM price by product ID
     *
     * @param int $productId Product Id
     * @return Oggetto_YandexMarket_Model_Price
     */
    public function loadByProductId($productId)
    {
        $this->load($productId);
        return $this;
    }

    /**
     * Get price representation formatted string
     *
     * @return string
     */
    public function getFormattedPrice()
    {
        return Mage::app()->getStore()->formatPrice($this->_getPrice());
    }

    /**
     * Is price available for this product
     *
     * @return bool
     */
    public function isExistPrice()
    {
        return $this->hasValue();
    }

    /**
     * Get price for this product
     *
     * @return float
     */
    protected function _getPrice()
    {
        return $this->getValue();
    }
}
