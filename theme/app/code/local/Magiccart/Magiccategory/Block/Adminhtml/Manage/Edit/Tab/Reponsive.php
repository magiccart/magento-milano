<?php
/**
 * Magiccart 
 * @category  Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license   http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2014-09-10 10:21:05
 * @@Modify Date: 2015-05-04 14:24:53
 * @@Function:
 */
?>
<?php
class Magiccart_Magiccategory_Block_Adminhtml_Manage_Edit_Tab_Reponsive extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$model = Mage::registry('magiccategory_data');	
		
		// if($model->getStores())
		// {		
		// 	//$_model->setPageId(Mage::helper('core')->jsonDecode($_model->getPageId()));
		// 	$model->setStores(explode(',',$model->getStores()));
		// }
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('reponsive_form', array('legend'=>Mage::helper('magiccategory')->__('General Information')));

        $fieldset->addField('portrait', 'select', array(
            'name'      => 'portrait',
            'label'     => Mage::helper('adminhtml')->__('Display in Screen 480:'),
            'title'     => Mage::helper('adminhtml')->__('Display in Screen 480:'),
            'values'	=> Mage::getSingleton('magiccategory/system_config_column')->toOptionArray(),
            'value'     => array(1),
        ));

        $fieldset->addField('landscape', 'select', array(
            'name'      => 'landscape',
            'label'     => Mage::helper('adminhtml')->__('Display in Screen 640:'),
            'title'     => Mage::helper('adminhtml')->__('Display in Screen 640:'),
            'values'	=> Mage::getSingleton('magiccategory/system_config_column')->toOptionArray(),
            'value'     => array(2),
        ));

        $fieldset->addField('tablet', 'select', array(
            'name'      => 'tablet',
            'label'     => Mage::helper('adminhtml')->__('Display in Screen 768:'),
            'title'     => Mage::helper('adminhtml')->__('Display in Screen 768:'),
            'values'	=> Mage::getSingleton('magiccategory/system_config_column')->toOptionArray(),
            'value'     => array(3),
        ));

        $fieldset->addField('desktop', 'select', array(
            'name'      => 'desktop',
            'label'     => Mage::helper('adminhtml')->__('Display in Screen 992:'),
            'title'     => Mage::helper('adminhtml')->__('Display in Screen 992:'),
            'values'	=> Mage::getSingleton('magiccategory/system_config_column')->toOptionArray(),
            'value'     => array(4),
        ));

        $fieldset->addField('visibleItems', 'select', array(
            'name'      => 'visibleItems',
            'label'     => Mage::helper('adminhtml')->__('Display Visible Items:'),
            'title'     => Mage::helper('adminhtml')->__('Display Visible Items:'),
            'values'	=> Mage::getSingleton('magiccategory/system_config_column')->toOptionArray(),
            'value'     => array(5),
        ));
		
		if (Mage::getSingleton('adminhtml/session')->getMagiccategoryData())
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getMagiccategoryData());
			Mage::getSingleton('adminhtml/session')->setMagiccategoryData(null);
		} elseif ( Mage::registry('magiccategory_data') ) {
            $data = Mage::registry('magiccategory_data')->getData();
            if($data) $form->setValues($data);
		}

        $this->setForm($form);

		return parent::_prepareForm();
	}
}
