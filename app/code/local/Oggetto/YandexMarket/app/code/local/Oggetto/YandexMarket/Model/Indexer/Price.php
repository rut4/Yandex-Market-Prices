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
 * Price indexer model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_YandexMarket_Model_Indexer_Price extends Mage_Index_Model_Indexer_Abstract
{
    protected $_matchedEntities = [
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_REINDEX,
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ]
    ];

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('yandex_market/indexer_price');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('yandex_market')->__('Yandex Market Price');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('yandex_market')->__('Yandex Market product prices');
    }

    /**
     * Register indexer required data inside event object
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        /* @var Mage_Catalog_Model_Product $entity */
        $entity = $event->getDataObject();
        if ($entity->getId()) {
            $event->setData('product_id', $entity->getId());
        } elseif ($entity->getProductIds()) {
            $event->setData('product_ids', $entity->getProductIds());
        }
    }

    /**
     * Process event based on event state data
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getData('product_id') || $event->getData('product_ids')) {
            $this->callEventHandler($event);
        }
    }
}
