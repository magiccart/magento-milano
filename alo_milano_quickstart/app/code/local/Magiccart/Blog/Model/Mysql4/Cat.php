<?php

class Magiccart_Blog_Model_Mysql4_Cat extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('blog/cat', 'cat_id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (strcmp($value, (int)$value) !== 0) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueIdentifier($object)) {
            Mage::throwException(Mage::helper('blog')->__('Post Identifier already exist.'));
        }

        if ($this->isNumericIdentifier($object)) {
            Mage::throwException(Mage::helper('blog')->__('Post Identifier cannot consist only of numbers.'));
        }

        return $this;
    }

    public function getIsUniqueIdentifier(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable() . '.identifier = ?', $object->getData('identifier'))
        ;
        if ($object->getId()) {
            $select->where($this->getMainTable() . '.cat_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    protected function isNumericIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('cat_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('cat_store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['cat_id'] = $object->getId();
            $storeArray['store_id'] = Mage::app()->getStore(true)->getId();
            $this->_getWriteAdapter()->insert($this->getTable('cat_store'), $storeArray);
        } else {
            foreach ((array)$object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['cat_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('cat_store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cat_store'))
            ->where('cat_id = ?', $object->getId())
        ;

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select
                ->join(array('cps' => $this->getTable('cat_store')), $this->getMainTable() . '.cat_id = `cps`.cat_id')
                ->where('`cps`.store_id in (0, ?) ', $object->getStoreId())
                ->order('store_id DESC')
                ->limit(1)
            ;
        }
        return $select;
    }
}