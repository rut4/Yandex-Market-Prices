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
 * YM parse model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Model_Parser
{
    /**
     * Prepare document to query
     *
     * @param string $document
     * @return Zend_Dom_Query
     */
    protected function _prepareParse($document)
    {
        $document = str_replace('nobr', 'span', $document);
        return new Zend_Dom_Query($document);
    }

    /**
     * Get node attribute by css query
     *
     * @param Zend_Dom_Query $domQuery
     * @param string $cssQuery
     * @param null $attribute
     * @return null|string
     */
    protected function _getAttributeByQuery(Zend_Dom_Query $domQuery, $cssQuery, $attribute = null)
    {
        $node = $domQuery->query($cssQuery)->current();
        if (is_null($node)) {
            return null;
        }
        if (is_null($attribute)) {
            return $node->nodeValue;
        }
        return $node->getAttribute($attribute);
    }

    /**
     * Parse document to find first item link
     *
     * @param string $document
     * @return null|string
     */
    public function parseFirstItemLink($document)
    {
        return $this->_getAttributeByQuery($this->_prepareParse($document), '.b-minicards__l a', 'href');
    }

    /**
     * Parse document to find price
     *
     * @param string $result
     * @return null|string
     */
    public function parsePrice($result)
    {
        $priceNodeValue = $this->_getAttributeByQuery($this->_prepareParse($result), '.b-price');
        if (is_null($priceNodeValue)) {
            return null;
        }
        return preg_replace('/[^\d]*/', '', $priceNodeValue);
    }
}
