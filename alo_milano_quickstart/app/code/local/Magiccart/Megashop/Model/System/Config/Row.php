<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-08-07 22:10:30
 * @@Modify Date: 2017-03-11 08:51:25
 * @@Function:
 */
?>
<?php
class Magiccart_Megashop_Model_System_Config_Row
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1,   'label'=>Mage::helper('adminhtml')->__('1 item per row')),
            array('value' => 2,   'label'=>Mage::helper('adminhtml')->__('2 items per row')),
            array('value' => 3,   'label'=>Mage::helper('adminhtml')->__('3 items per row')),
            array('value' => 4,   'label'=>Mage::helper('adminhtml')->__('4 items per row')),
            array('value' => 5,   'label'=>Mage::helper('adminhtml')->__('5 items per row')),
        );
    }
}
