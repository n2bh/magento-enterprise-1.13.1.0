<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * RMA entity resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Model_Resource_Item extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes   = array();

    /**
     * Array of aviable items types for rma
     *
     * @var array
     */
    protected $_aviableProductTypes = array(
        Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
    );

    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->setType('rma_item');
        $this->setConnection('rma_item_read', 'rma_item_write');
    }

    /**
     * Redeclare attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return 'enterprise_rma/item_attribute';
    }

    /**
     * Returns default Store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Check whether the attribute is Applicable to the object
     *
     * @param Varien_Object $object
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return boolean
     */
    protected function _isApplicableAttribute($object, $attribute)
    {
        $applyTo = $attribute->getApplyTo();
        return count($applyTo) == 0 || in_array($object->getTypeId(), $applyTo);
    }

    /**
     * Check whether attribute instance (attribute, backend, frontend or source) has method and applicable
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|Mage_Eav_Model_Entity_Attribute_Backend_Abstract|Mage_Eav_Model_Entity_Attribute_Frontend_Abstract|Mage_Eav_Model_Entity_Attribute_Source_Abstract $instance
     * @param string $method
     * @param array $args array of arguments
     * @return boolean
     */
    protected function _isCallableAttributeInstance($instance, $method, $args)
    {
        if ($instance instanceof Mage_Eav_Model_Entity_Attribute_Backend_Abstract
            && ($method == 'beforeSave' || $method = 'afterSave')
        ) {
            $attributeCode = $instance->getAttribute()->getAttributeCode();
            if (isset($args[0]) && $args[0] instanceof Varien_Object && $args[0]->getData($attributeCode) === false) {
                return false;
            }
        }

        return parent::_isCallableAttributeInstance($instance, $method, $args);
    }

    /**
     * Reset firstly loaded attributes
     *
     * @param Varien_Object $object
     * @param integer $entityId
     * @param array|null $attributes
     * @return Mage_Catalog_Model_Resource_Abstract
     */
    public function load($object, $entityId, $attributes = array())
    {
        $this->_attributes = array();
        return parent::load($object, $entityId, $attributes);
    }

    /**
     * Gets rma authorized items ids an qty by rma id
     *
     * @param  int $rmaId
     * @return array
     */
    public function getAuthorizedItems($rmaId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('enterprise_rma/item_entity'), array())
            ->where('rma_entity_id = ?', $rmaId)
            ->where('status = ?', Enterprise_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED)
            ->group(array('order_item_id', 'product_name'))
            ->columns(
                array(
                    'order_item_id' => 'order_item_id',
                    'qty'           => new Zend_Db_Expr('SUM(qty_authorized)'),
                    'product_name'  => new Zend_Db_Expr('MAX(product_name)')
                )
            )
        ;

        $return     = $adapter->fetchAll($select);
        $itemsArray = array();
        if (!empty($return)) {
            foreach ($return as $item) {
                $itemsArray[$item['order_item_id']] = $item;
            }
        }
        return $itemsArray;
    }

    /**
     * Gets rma items ids by order
     *
     * @param  int $orderId
     * @return array
     */
    public function getItemsIdsByOrder($orderId)
    {
        $adapter = $this->_getReadAdapter();

        $subSelect = $adapter->select()
            ->from(
                array('main' => $this->getTable('enterprise_rma/rma')),
                array()
            )
            ->where('main.order_id = ?', $orderId)
            ->where('main.status NOT IN (?)',
                array(
                    Enterprise_Rma_Model_Rma_Source_Status::STATE_CLOSED,
                    Enterprise_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED
                )
            );

        $select = $adapter->select()
            ->from(
                array('item_entity' => $this->getTable('enterprise_rma/item_entity')),
                array('item_entity.order_item_id','item_entity.order_item_id')
            )
            ->exists($subSelect, 'main.entity_id = item_entity.rma_entity_id');

        return array_values($adapter->fetchPairs($select));
    }

    /**
     * Gets order items collection
     *
     * @param  int $orderId
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getOrderItemsCollection($orderId)
    {
        return Mage::getModel('sales/order_item')
            ->getCollection()
            ->addExpressionFieldToSelect(
                'available_qty',
                '(qty_shipped - qty_returned)',
                array('qty_shipped', 'qty_returned')
            )
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('product_type', array("in" => $this->_aviableProductTypes))
            ->addFieldToFilter('(qty_shipped - qty_returned)', array("gt" => 0));
    }

    /**
     * Gets available order items collection
     *
     * @param  int $orderId
     * @param  int|bool $parentId if need retrieves only bundle and its children
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getOrderItems($orderId, $parentId = false)
    {
        $getItemsIdsByOrder     = $this->getItemsIdsByOrder($orderId);

        /** @var $orderItemsCollection Mage_Sales_Model_Resource_Order_Item_Collection */
        $orderItemsCollection   = $this->getOrderItemsCollection($orderId);


        if (!$orderItemsCollection->count()) {
            return $orderItemsCollection;
        }

        /**
         * contains data that defines possibility of return for an order item
         * array value ['self'] refers to item's own rules
         * array value ['child'] refers to rules defined from item's sub-items
         */
        $parent = array();

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');

        foreach ($orderItemsCollection as $item) {
            /* retrieves only bundle and children by $parentId */
            if ($parentId && ($item->getId() != $parentId) && ($item->getParentItemId() != $parentId)) {
                $orderItemsCollection->removeItemByKey($item->getId());
                continue;
            }

            $allowed = true;
            /* checks item in active rma */
            if (in_array($item->getId(), $getItemsIdsByOrder)) {
                $allowed = false;
            }


            /* checks enable on product level */
            $product->reset();
            $product->setStoreId($item->getStoreId());
            try {
                $product->load($item->getProductId());
            } catch (Enterprise_AdminGws_Controller_Exception $e) {
                Mage::logException($e);
                $allowed = false;
            }

            if (!Mage::helper('enterprise_rma')->canReturnProduct($product, $item->getStoreId())) {
                $allowed = false;
            }

            if ($item->getParentItemId()) {
                if (!isset($parent[$item->getParentItemId()]['child'])) {
                    $parent[$item->getParentItemId()]['child'] = false;
                }
                if (!$allowed) {
                    $item->setIsOrdered(1);
                    $item->setAvailableQty($item->getQtyShipped()-$item->getQtyRefunded()-$item->getQtyCanceled());
                }
                $parent[$item->getParentItemId()]['child']  = $parent[$item->getParentItemId()]['child'] || $allowed;
                $parent[$item->getItemId()]['self']         = false;
            } else {
                $parent[$item->getItemId()]['self']         = $allowed;
            }
        }

        $bundle = false;
        foreach ($orderItemsCollection as $item) {
            if (isset($parent[$item->getId()]['child'])
                && ($parent[$item->getId()]['child'] === false || $parent[$item->getId()]['self'] == false)
            ) {
                $orderItemsCollection->removeItemByKey($item->getId());
                $bundle = $item->getId();
                continue;
            }

            if ($bundle && $item->getParentItemId() && $bundle == $item->getParentItemId()) {
                $orderItemsCollection->removeItemByKey($item->getId());
            } elseif (isset($parent[$item->getId()]['self']) && $parent[$item->getId()]['self'] === false) {
                if ($item->getParentItemId() && $bundle != $item->getParentItemId()) {

                } else {
                    $orderItemsCollection->removeItemByKey($item->getId());
                    continue;
                }
            }

            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                && !isset($parent[$item->getId()]['child'])
            ) {
                $orderItemsCollection->removeItemByKey($item->getId());
                continue;
            }

            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $productOptions     = $item->getProductOptions();
                $product->reset();
                $product->load($product->getIdBySku($productOptions['simple_sku']));
                if (!Mage::helper('enterprise_rma')->canReturnProduct($product, $item->getStoreId())) {
                    $orderItemsCollection->removeItemByKey($item->getId());
                    continue;
                }
            }

            $item->setName($this->getProductName($item));
        }

        return $orderItemsCollection;
    }

    /**
     * Gets Product Name
     *
     * @param $item Mage_Sales_Model_Order_Item
     * @return string
     */
    public function getProductName($item)
    {
        $name   = $item->getName();
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            if (!empty($result)) {
                $implode = array();
                foreach ($result as $val) {
                    $implode[] =  isset($val['print_value']) ? $val['print_value'] : $val['value'];
                }
                return $name.' ('.implode(', ', $implode).')';
            }
        }
        return $name;
    }
}
