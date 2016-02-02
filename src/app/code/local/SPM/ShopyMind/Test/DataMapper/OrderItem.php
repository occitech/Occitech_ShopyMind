<?php

/**
 * @group dataMappers
 * @loadSharedFixture
 * @group spm
 */
class SPM_ShopyMind_Test_Lib_ShopymindClient_DataMapper_OrderItem extends EcomDev_PHPUnit_Test_Case
{
    public $SUT;

    public function setup() {
        parent::setup();
        $this->SUT = new SPM_ShopyMind_DataMapper_OrderItem();
    }

    public function testFormatProductItem()
    {
        $productItem = Mage::getModel('sales/order_item')->load(1);
        $quote = Mage::getModel('sales/order')->load(1);
        $productItem->setQuote($quote);

        $expected = array(
            'id_product' => 1,
            'id_combination' => 1,
            'id_manufacturer' => null,
            'price' => 3.5000,
            'qty' => 8.0000,
        );

        $actual = $this->SUT->format($productItem);
        $this->assertEquals($expected, $actual);
    }

}
