<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-08-07 22:10:30
 * @@Modify Date: 2015-07-01 10:11:39
 * @@Function:
 */

class Magiccart_Magicmenu_Model_System_Config_Category
{
 
    public function toOptionArray() {
        $catTop = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToSelect(array('entity_id','name'))
                        ->addIsActiveFilter()
                        ->addAttributeToFilter('level',2) // note: ->addAttributeToFilter('level',2) =! ->addLevelFilter(2)
                        ->addOrderField('position');
        $options = array();
        foreach ($catTop as $cat) {
            $options[] = array(
                'value' =>  $cat->getEntityId(),
                'label' =>  $cat->getName()
            );  
        }
        return $options;
    }
 

}
