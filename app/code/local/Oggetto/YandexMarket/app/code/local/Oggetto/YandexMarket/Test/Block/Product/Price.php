<?php
class Oggetto_YandexMarket_Test_Block_Product_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test product price block returns product price
     *
     * @param int    $productId      Product id
     * @param string $formattedPrice Formatted price
     * @dataProvider dataProvider
     * @return void
     */
    public function testReturnsProductPrice($productId, $formattedPrice)
    {
        $price = $this->getModelMock('yandex_market/price', ['loadByProductId', 'isExistPrice', 'getFormattedPrice']);

        $price->expects($this->once())
            ->method('loadByProductId')
            ->with($this->equalTo($productId))
            ->will($this->returnSelf());

        $price->expects($this->once())
            ->method('isExistPrice')
            ->will($this->returnValue(true));

        $price->expects($this->once())
            ->method('getFormattedPrice')
            ->will($this->returnValue($formattedPrice));

        $this->replaceByMock('model', 'yandex_market/price', $price);

        $block = new Oggetto_YandexMarket_Block_Product_Price;
        $this->assertEquals($formattedPrice, $block->getPrice($productId));
    }

    /**
     * Test product price block doesn't return product price when it's unavailable
     *
     * @return void
     */
    public function testDoesNotReturnProductPriceWhenItIsUnavailable()
    {
        $price = $this->getModelMock('yandex_market/price', ['loadByProductId', 'isExistPrice']);

        $price->expects($this->exactly(2))
            ->method('loadByProductId')
            ->with($this->anything())
            ->will($this->returnSelf());

        $price->expects($this->exactly(2))
            ->method('isExistPrice')
            ->will($this->returnValue(false));

        $this->replaceByMock('model', 'yandex_market/price', $price);

        $block = new Oggetto_YandexMarket_Block_Product_Price;

        $block->getPrice(1);
        $block->getPrice(2);
    }

}
