<?php

class Oggetto_YandexMarket_Test_Model_Price extends EcomDev_PHPUnit_Test_Case
{
    public function testLoadsItselfByProductId()
    {
        $priceMock = $this->getModelMock('yandex_market/price', ['load']);

        $priceMock->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo(42));

        $priceMock->expects($this->at(1))
            ->method('load')
            ->with($this->equalTo(115));

        $priceMock->loadByProductId(42);
        $priceMock->loadByProductId(115);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReturnsFormattedPrice($price, $formatted)
    {
        $priceMock = $this->getModelMock('yandex_market/price', ['getValue']);

        $priceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($price));

        $this->assertEquals($formatted, $priceMock->getFormattedPrice());
    }
}
