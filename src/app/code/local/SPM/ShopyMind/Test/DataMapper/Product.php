<?php

/**
 * @group dataMappers
 * @loadSharedFixture
 * @group productMapper
 */
class SPM_ShopyMind_Test_DataMapper_Product extends EcomDev_PHPUnit_Test_Case
{
    public $SUT;

    public function setup() {
        parent::setup();
        $this->SUT = new SPM_ShopyMind_DataMapper_Product();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $write = Mage::getSingleton('core/resource')->getConnection('write');
        $write->query(<<<'QUERY'
        TRUNCATE catalog_product_entity_varchar;
        TRUNCATE catalog_product_entity_int;
        TRUNCATE catalog_product_entity_decimal;
        TRUNCATE catalog_product_entity_text;
        TRUNCATE catalog_category_product;
        TRUNCATE catalog_product_entity;
QUERY
        );
    }

    public function testFormattingEmptyProductReturnsNoData()
    {
        $unknownProduct = Mage::getModel('catalog/product')->load(101467987);
        $actual = $this->SUT->format($unknownProduct);
        $this->assertEquals(array(), $actual);
    }

    public function testFormattingSimpleProductReturnsCorrectDataWithoutScopeRelatedInformation()
    {
        $simpleProduct = Mage::getModel('catalog/product')->load(1);
        $actual = $this->SUT->format($simpleProduct);
        unset($actual['image_link'], $actual['product_link']);

        $expectedData = array(
            'id_product' => 1,
            'reference' => 'sku42-pr',
            'name' => 'First simple product',
            'description_short' => 'This is a short description',
            'description' => 'This is a long description',
            'combinations' => array(),
            'id_categories' => array(1, 2),
            'id_manufacturer' => null,
            'price' => 13.00,
            'price_discount' => 13.00,
            'quantity_remaining' => 100,
            'date_creation' => '2013-03-05 05:48:12',
            'active' => true,
        );

        // Change assertion to match Magento 1.8.x core flaw with createdAt dates...
        // See http://magento.stackexchange.com/a/31225
        $versionInfo = Mage::getVersionInfo();
        if ($versionInfo['major'] === '1' && $versionInfo['minor'] === '8') {
            $expectedData['date_creation'] = '2013-05-03 05:48:12'; // This is a known behavior on an unpatched Magento 1.8 month/day will be switched
        }

        $this->assertEquals($expectedData, $actual);
    }

    public function testFormattingProductReturnsCorrectSpecialPriceWhenProductHasOne()
    {
        $simpleProduct = Mage::getModel('catalog/product')->load(1);
        $simpleProduct->setSpecialPrice(10.00);
        $actual = $this->SUT->format($simpleProduct);

        $this->assertEquals(10.00, $actual['price_discount']);
    }

    public function testFormattingProductReturnsInactiveWhenProductIsNotInStock()
    {
        $simpleProduct = Mage::getModel('catalog/product')->load(1);
        $stockItem = $simpleProduct->getStockItem();
        $stockItem->setIsInStock(0);
        $this->assertTrue((bool) $stockItem->save(), 'precondition failed');

        $simpleProduct = Mage::getModel('catalog/product')->load(1);
        $actual = $this->SUT->format($simpleProduct);

        $this->assertFalse($actual['active']);
    }

    /**
     * @ loadFixture
     * TODO Make me pass faster
     */
    public function testFormattingConfigurableProductReturnsCombinationsData()
    {
        $this->markTestIncomplete('TODO Make it faster and stable, I stumbled upon setting it up properly in a reasonable time');
        $configurableProduct = Mage::getModel('catalog/product')->load(2);
        $actual = $this->SUT->format($configurableProduct);

        $expectedCombinations = array (
            array(
                'reference' => 'sku43-configurable-pr',
                'product_link' => 'catalog/product/view/id/1/s/first-simple-product/',
                'image_link' => 'media/catalog/product/cache/1/small_image/200x200/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/small_image.jpg',
                'combination_name' => 'First simple product',
                'id_combination' => 1,
                'values' => array(
                    'color' => 'white',
                    'size' => 'XL',
                ),
                'price' => 13.00,
                'price_discount' => 13.00,
                'quantity_remaining' => 100,
            )
        );

        $this->assertEquals($expectedCombinations, $actual['combinations']);
    }
}
