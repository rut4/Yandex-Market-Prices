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
 * Data helper class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Helper
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get authorization key
     *
     * @return string
     */
    public function getAuthorizationKey()
    {
        return Mage::getStoreConfig('yandex_market_price/general/ratio_greater_price');
    }

    /**
     * Get ratio for price if it's greater then YM price
     *
     * @return float
     */
    public function getRatioForGreaterPrice()
    {
        return floatval(Mage::getStoreConfig('yandex_market_price/general/ratio_greater_price'));
    }
}
