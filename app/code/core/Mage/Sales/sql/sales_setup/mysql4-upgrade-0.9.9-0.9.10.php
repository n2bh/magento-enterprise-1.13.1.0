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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

// add FK constraint on products to flat quote items

$installer->run("
DELETE FROM `{$this->getTable('sales_flat_quote_item')}`
WHERE `product_id` NOT IN (
    SELECT `entity_id` FROM `{$this->getTable('catalog_product_entity')}`
)
");

$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_ITEM_CATALOG_PRODUCT_ENTITY',
    $this->getTable('sales_flat_quote_item'), 'product_id',
    $this->getTable('catalog_product_entity'), 'entity_id'
);
