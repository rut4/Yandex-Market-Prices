<?php

class Oggetto_YandexMarket_Test_Model_Indexer_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test price indexer model is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf(
            'Oggetto_YandexMarket_Model_Indexer_Price',
            Mage::getModel('yandex_market/indexer_price')
        );
    }

    /**
     * Test price indexer initializations with resource model
     *
     * @return void
     */
    public function testInitializationsWithResourceModel()
    {
        $this->assertInstanceOf(
            'Oggetto_YandexMarket_Model_Resource_Indexer_Price',
            Mage::getModel('yandex_market/indexer_price')->getResource()
        );
    }

    /**
     * Test price indexer returns name
     *
     * @return void
     */
    public function testReturnsName()
    {
        $helper = $this->getHelperMock('yandex_market/data', ['__']);

        $helper->expects($this->once())
            ->method('__')
            ->with('Yandex Market Price')
            ->will($this->returnArgument(0));

        $this->replaceByMock('helper', 'yandex_market', $helper);

        $this->assertEquals('Yandex Market Price', Mage::getModel('yandex_market/indexer_price')->getName());
    }

    /**
     * Test price indexer returns description
     *
     * @return void
     */
    public function testReturnsDescription()
    {
        $helper = $this->getHelperMock('yandex_market/data', ['__']);

        $helper->expects($this->once())
            ->method('__')
            ->with('Yandex Market product prices')
            ->will($this->returnArgument(0));

        $this->replaceByMock('helper', 'yandex_market', $helper);

        $this->assertEquals(
            'Yandex Market product prices',
            Mage::getModel('yandex_market/indexer_price')->getDescription()
        );
    }

    /**
     * Prepare product event
     *
     * @param string $eventType Event type
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareEvent($eventType)
    {
        $event = $this->getModelMock('index/event', ['getEntity', 'getType']);

        $event->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue(Mage_Catalog_Model_Product::ENTITY));

        $event->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($eventType));

        return $event;
    }

    /**
     * Test price indexer matches product type reindex event
     *
     * @return void
     */
    public function testMatchesReindexEvent()
    {
        $this->assertTrue(
            Mage::getModel('yandex_market/indexer_price')->matchEvent(
                $this->_prepareEvent(Mage_Index_Model_Event::TYPE_REINDEX)
            )
        );
    }

    /**
     * Test price indexer matches product type save event
     *
     * @return void
     */
    public function testMatchesSaveEvent()
    {
        $this->assertTrue(
            Mage::getModel('yandex_market/indexer_price')->matchEvent(
                $this->_prepareEvent(Mage_Index_Model_Event::TYPE_SAVE)
            )
        );
    }

    /**
     * Test price indexer matches product type mass action event
     *
     * @return void
     */
    public function testMatchesMassActionEvent()
    {
        $this->assertTrue(
            Mage::getModel('yandex_market/indexer_price')->matchEvent(
                $this->_prepareEvent(Mage_Index_Model_Event::TYPE_MASS_ACTION)
            )
        );
    }

    /**
     * Test price indexer register product save event
     *
     * @param int $productId Product id
     * @dataProvider dataProvider
     * @return void
     */
    public function testRegisterSaveEvent($productId)
    {
        $product = $this->getModelMock('catalog/product', ['getId']);

        $product->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($productId));

        $this->replaceByMock('model', 'catalog/product', $product);

        $event = $this->getModelMock('index/event', ['getDataObject', 'setData']);

        $event->expects($this->once())
            ->method('getDataObject')
            ->will($this->returnValue($product));

        $event->expects($this->once())
            ->method('setData')
            ->with(
                'product_id',
                $this->equalTo($productId)
            );

        $this->replaceByMock('model', 'index/event', $event);

        $priceIndexer = $this->getModelMock('yandex_market/indexer_price', ['matchEvent']);

        $priceIndexer->expects($this->once())
            ->method('matchEvent')
            ->with($this->equalTo($event))
            ->will($this->returnValue(true));

        $priceIndexer->register($event);
    }

    /**
     * Test price indexer register product mass action event
     *
     * @param int $productIds Product ids
     * @dataProvider dataProvider
     * @return void
     */
    public function testRegisterMassActionEvent($productIds)
    {
        $product = $this->getModelMock('catalog/product', ['getProductIds']);

        $product->expects($this->exactly(2))
            ->method('getProductIds')
            ->will($this->returnValue($productIds));

        $this->replaceByMock('model', 'catalog/product', $product);

        $event = $this->getModelMock('index/event', ['getDataObject', 'setData']);

        $event->expects($this->once())
            ->method('getDataObject')
            ->will($this->returnValue($product));

        $event->expects($this->once())
            ->method('setData')
            ->with(
                'product_ids',
                $this->equalTo($productIds)
            );

        $this->replaceByMock('model', 'index/event', $event);

        $priceIndexer = $this->getModelMock('yandex_market/indexer_price', ['matchEvent']);

        $priceIndexer->expects($this->once())
            ->method('matchEvent')
            ->with($this->equalTo($event))
            ->will($this->returnValue(true));

        $priceIndexer->register($event);
    }
}
