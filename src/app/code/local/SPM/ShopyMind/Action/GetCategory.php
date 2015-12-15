<?php

class SPM_ShopyMind_Action_GetCategory implements SPM_ShopyMind_Interface_Action
{
    private $scope;
    private $categoryId;
    private $Category;
    private $Formatter;

    public function __construct(SPM_ShopyMind_Model_Scope $scope, $categoryId)
    {
        $this->Category = Mage::getModel('catalog/category');

        $this->categoryId = $categoryId;
        $this->scope = $scope;
    }

    public function process($formatData = true)
    {
        $storeIds = $this->scope->storeIds();
        /** @var  $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $emulatedEnvironment = $appEmulation->startEnvironmentEmulation($storeIds[0]);
        $collection = $this->Category->getCollection();
        $this->scope->restrictCategoryCollection($collection);
        $collection->addAttributeToFilter('entity_id', array('eq' => $this->categoryId))
            ->addAttributeToSelect('*');

        $category = $collection->getFirstItem();

        if (is_null($this->categoryId) || !$category->getId()) {
            return array();
        }
        $this->Formatter = new SPM_ShopyMind_DataMapper_Category($category);
        $locale = $this->scope->getConfig('general/locale/code');
        $category->setData('locale', (string) $locale);
        $data = $formatData ? $this->Formatter->format() : $category;
        $appEmulation->stopEnvironmentEmulation($emulatedEnvironment);

        return $data;
    }

}