<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-07-15 20:37:16
 * @@Modify Date: 2016-03-11 15:27:40
 * @@Function:
 */

class Magiccart_Magicsocial_Model_System_Config_Source_Socialnetwork
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'instagram', 'label'=>Mage::helper('adminhtml')->__('Instagram')),
            array('value' => 'pinterest', 'label'=>Mage::helper('adminhtml')->__('Pinterest')),
            array('value' => 'flickr', 'label'=>Mage::helper('adminhtml')->__('Flickr')),
            array('value' => 'picasa', 'label'=>Mage::helper('adminhtml')->__('Picasa')),
        );
    }

}
