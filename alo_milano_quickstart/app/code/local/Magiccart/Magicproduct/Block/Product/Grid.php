<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-03-14 20:26:27
 * @@Modify Date: 2015-05-04 15:54:39
 * @@Function:
 */
?>
<?php
class Magiccart_Magicproduct_Block_Product_Grid extends Mage_Catalog_Block_Product_List
{

    // protected $_productCollection;

    public function getType()
    {
        $type = $this->getRequest()->getParam('type');
        if(!$type){
            $type = $this->getActive(); // get form setData in Block
        }
        return $type;
    }

    public function getWidgetCfg($cfg=null)
    {
        $info = $this->getRequest()->getParam('info');
        if($info){
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;          
        }else {
            $info = $this->getCfg();
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;
        }
    }

    public function isRootCategoryFilterMode()
    {
        if(!$this->isSingleCateogryMode()) return Mage::app()->getStore()->getRootCategoryId();
    }

    public function isSingleCateogryMode(){
        $groups = Mage::app()->getGroups();
        if(count($groups) ==1) return true;
        $CatIds = array();
        foreach ($groups as $group) {
            $CatIds[] = $group->getRootCategoryId();
        }
        $average = array_sum($CatIds) / count($CatIds);
        if($average == $CatIds[0]) return true;
        return false;
    }

    public function categoryFilter($collection)
    {
        $cfg = true; // get from config or Widget
        if($cfg){
            $catId = (int) $this->getRequest()->getPost('category_id');
            // if(!$catId) {$catId = Mage::registry('current_category');}
            if($catId){
                $Category = Mage::getModel('catalog/category')->load($catId);
                $collection->addCategoryFilter($Category); // not use RootCatId
            }else {
                $catId = $this->isRootCategoryFilterMode();
                if($catId){
                    // $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                    //            ->addAttributeToFilter('category_id', array('in' => $catId));
                    $category = Mage::getModel('catalog/category')->load($catId);
                    //$catIds = explode(',',$category->getChildren());
                    $catIds = explode(',',$category->getAllChildren());
                    $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                               ->addAttributeToFilter('category_id', array('in' => $catIds))
                               ->groupByAttribute('entity_id');    //->getSelect()->group('e.entity_id');        
                }
            }
        }
        return $collection;
    }

    protected function _getProductCollection()
    {
        // if (is_null($this->_productCollection)) {
            $type = $this->getType();
            switch ($type) {
              case 'bestseller':
                $collection = $this->getBestsellerProducts();
                break;
              case 'featured':
                $collection = $this->getFeaturedProducts();
                break;
              case 'latest':
                $collection = $this->getLatestProducts();
                break;
              case 'mostviewed':
                $collection = $this->getMostviewedProducts();
                break;
              case 'newproduct':
                $collection = $this->getNewProducts();
                break;
              case 'random':
                $collection = $this->getRandomProducts();
                break;
              // case 'review':
              //   $Collection = $this->getReviewProducts();
              //   break;
              case 'saleproduct':
                $collection = $this->getSaleProducts();
                break;
              case 'specialproduct':
                $collection = $this->getSpecialProducts();
                break;
              default:
                $collection = $this->getMostviewedProducts();
                break;
            }
            $this->_productCollection = $collection;
        // }
        return $this->_productCollection;
    }


    public function getBestsellerProducts(){

        // set Store
        $storeIds = Mage::app()->getGroup()->getStoreIds(); // filter follow store;
        //$storeIds  = $this->getStoreId(); // filter follow store view
        $storeId = implode(',', $storeIds);

        // set Time
        $timePeriod = ($this->getTimePeriod()) ? $this->getTimePeriod() : 365;
        $date = date('Y-m-d H:i:s');
        $newdate = strtotime ( '-'.$timePeriod.' day' , strtotime ( $date ) ) ;
        $newdate = date ( 'Y-m-j' , $newdate ); 

        // set Limit
        $limit    = $this->getWidgetCfg('limit'); 
        if(!$limit) $limit = 6;

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = Mage::getConfig()->getTablePrefix();

        $sql = "SELECT max(qo) AS des_qty,`product_id`,`parent_item_id`
            FROM ( SELECT sum(`qty_ordered`) AS qo,`product_id`,created_at,store_id,`parent_item_id` FROM {$tablePrefix}sales_flat_order_item GROUP BY `product_id` )
            AS t1 where store_id IN ({$storeId})
            AND parent_item_id is null
            AND created_at between '{$newdate}' AND '{$date}'
            GROUP BY `product_id` ORDER BY des_qty DESC LIMIT {$limit}";

        // Note: remove limit if filter follow category

        $rows = $read->fetchAll($sql);
        $producIds = array();
        foreach ($rows as $row) { $producIds[] = $row['product_id'];}
        $collection = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())              
                            ->addAttributeToFilter('entity_id', array('in' => $producIds));
        return $collection;
    }

    public function getFeaturedProducts(){
        $featured   = Magiccart_Magicproduct_Helper_Data::FEATURED;
        $collection = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addAttributeToFilter($featured, 1, 'left')
                            ->addMinimalPrice()
                            ->addTaxPercents()
                            ->addStoreFilter();

        // CategoryFilter
        $collection = $this->categoryFilter($collection);

        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));
        return $collection; 
    }

    public function getLatestProducts(){

        $collection = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addMinimalPrice()
                            ->addTaxPercents()
                            ->addStoreFilter()
                            ->addAttributeToSort('entity_id', 'desc');

        // CategoryFilter
        $collection = $this->categoryFilter($collection);

        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));
        return $collection; 
    }

    public function getMostviewedProducts(){
     //Magento get popular products by total number of views
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $collection = Mage::getResourceModel('reports/product_collection')
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect($attributes)
                            ->addViewsCount()
                            ->addMinimalPrice()
                            ->addTaxPercents()
                            ->addStoreFilter(); 

        // CategoryFilter
        $collection = $this->categoryFilter($collection);
        $collection->setPageSize($this->getWidgetCfg('limit'));
        $productFlatHelper = Mage::helper('catalog/product_flat');
        if($productFlatHelper->isEnabled())
        {
            // fix error lost image vs name while Enable useFlatCatalogProduct
            $productIds = array();
            foreach ($collection as $product) 
            {
                $productIds[] = $product->getEntityId();
            }
            $collection = Mage::getModel('catalog/product')
                            ->getCollection() 
                            ->addAttributeToSelect($attributes)              
                            ->addAttributeToFilter('entity_id', array('in' => $productIds));       
        }

        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));

        return $collection;
    }

    public function getNewProducts() {

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addAttributeToSelect('*') //Need this so products show up correctly in product listing
                            ->addAttributeToFilter('news_from_date', array('or'=> array(
                                0 => array('date' => true, 'to' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter('news_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                    )
                              )
                            ->addAttributeToSort('news_from_date', 'desc')
                            ->addMinimalPrice()
                            ->addTaxPercents()
                            ->addStoreFilter(); 

        // CategoryFilter
        $collection = $this->categoryFilter($collection);

        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));
        return $collection;
    }

    public function getRandomProducts() {

        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addMinimalPrice()
                            ->addTaxPercents()
                            ->addStoreFilter();
        $collection->getSelect()->order('rand()');

        // CategoryFilter
        $collection = $this->categoryFilter($collection);

        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));
        return $collection;
    }

    public function getReviewProducts() {

    }

    public function getSaleProducts(){

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addAttributeToFilter('special_from_date', array('or'=> array(
                                0 => array('date' => true, 'to' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter('special_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                    array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                    )
                              )
                            ->addAttributeToSort('special_to_date','desc')
                            ->addTaxPercents()
                            ->addStoreFilter();

        // CategoryFilter
        $collection = $this->categoryFilter($collection);

        // get Sale off
        // foreach ($collection as $key => $product) {
        //     if($product->getSpecialPrice() == '') $collection->removeItemByKey($key); // remove product not set SpecialPrice
        //     if($product->getSpecialPrice() && $product->getSpecialPrice() >= $product->getPrice())
        //     {
        //        $collection->removeItemByKey($key); // remove product price increase
        //     }
        // }
        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));
        return $collection;

    }

    public function getSpecialProducts() {

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addAttributeToFilter('special_from_date', array('or'=> array(
                                0 => array('date' => true, 'to' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter('special_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                    array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                    )
                              )
                            ->addAttributeToSort('special_to_date','desc')
                            ->addTaxPercents()
                            ->addStoreFilter();    

        // CategoryFilter
        $collection = $this->categoryFilter($collection);

        // getNumProduct
        $collection->setPageSize($this->getWidgetCfg('limit'));
        return $collection;
    }

}
