<?php

/**
 * Class SPM_ShopyMind_Test_Action_GetCategory
 * @group category
 * @loadSharedFixture
 */
class SPM_ShopyMind_Test_Action_GetCategory extends EcomDev_PHPUnit_Test_Case
{

    public function testProcessWithNotFoundCategoryReturnsEmptyArray()
    {
        $scope = SPM_ShopyMind_Model_Scope::fromShopymindId('');
        $Action = new SPM_ShopyMind_Action_GetCategory($scope, null);

        $this->assertEmpty($Action->process());
    }

    public function testProcessWithExistingCategoryId()
    {
        $scope = SPM_ShopyMind_Model_Scope::fromShopymindId('');
        $Action = new SPM_ShopyMind_Action_GetCategory($scope, 6);

        $expected = array(
            'entity_id' => 6,
            'parent_id' => 5,
            'name' => 'Hello world',
            'description' => 'this is REALLY a test category',
            'url_key' => 'hello-world',
            'created_at' => '2013-10-26 12:00:00',
            'is_active' => 1,
            'entity_type_id' => 3,
            'attribute_set_id' => 3,
            'updated_at' => '2013-10-26 13:00:00',
            'path' => '1/5/6',
            'position' => '1',
            'level' => 1,
            'children_count' => 0,
            'include_in_menu' => 1
        );

        $category = $Action->process();
        $this->assertEquals($expected, $category->getData());
    }

}
