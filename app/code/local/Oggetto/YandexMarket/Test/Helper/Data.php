<?php

class Oggetto_YandexMarket_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    public function testReturnsAuthorizationKey()
    {
        $this->assertEquals(
            Mage::getStoreConfig('yandex_market_price/general/auth_key'),
            Mage::helper('yandex_market')->getAuthorizationKey()
        );
    }

    public function testReturnsRatioForGreaterPrice()
    {
        $this->assertEquals(
            Mage::getStoreConfig('yandex_market_price/general/ratio_greater_price'),
            Mage::helper('yandex_market')->getAuthorizationKey()
        );
    }
}
