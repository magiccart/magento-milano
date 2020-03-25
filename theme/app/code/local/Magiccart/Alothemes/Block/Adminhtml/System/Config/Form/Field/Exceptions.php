<?php

class Magiccart_Alothemes_Block_Adminhtml_System_Config_Form_Field_Exceptions
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    private $developer;

    public function __construct()
    {

        $this->developer = Mage::getStoreConfig("alodesign/general/developer");

        $this->addColumn('title', array(
            'label' => $this->__('Title'),
            'style' => 'width:180px',
            'class' => 'title',
        ));

        $this->addColumn('selector', array(
            'label' => $this->__('Selector'),
            'style' => $this->developer ? 'width:180px;' : 'display:none;',
            'class' => 'selector',
        ));

        $this->addColumn('color', array(
            'label' => $this->__('Color :'),
            'style' => 'width:116px',
            'class' =>  $this->classColor(),
        ));  

        $this->addColumn('background', array(
            'label' => $this->__('background-color :'),
            'style' => 'width:116px',
            'class' =>  $this->classColor(),
        )); 

        $this->addColumn('border', array(
            'label' => $this->__('border-color :'),
            'style' => 'width:116px',
            'class' =>  $this->classColor(),
        )); 
             
        $this->_addAfter = true;
        $this->_addButtonLabel = $this->__('Add Config Color');
        // if (!$this->getTemplate()) {
        //     $this->setTemplate('magiccart/alothemes/system/config/form/field/array.phtml');
        // }
        parent::__construct();
    }

    public function classColor()
    {
        // $store = Mage::app()->getStore($storeCode);
        return $this->developer ? 'alo-color' : 'alo-color alo-readonly';
    }

    public function addColumn($name, $params)
    {
        $developer = Mage::getStoreConfig("alodesign/general/developer");
        if($developer) $label = $params['label'];
        else {
            $label = ($name != 'selector') ? $params['label'] : '';
        }
        $this->_columns[$name] = array(
            'label'     => $label, // $developer ? $params['label'] : '', // empty($params['label']) ? 'Column' : $params['label'],
            'size'      => empty($params['size'])  ? false    : $params['size'],
            'style'     => empty($params['style'])  ? null    : $params['style'],
            'class'     => empty($params['class'])  ? null    : $params['class'],
            'renderer'  => false,
        );
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Mage_Core_Block_Abstract)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

}
