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

$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'base_tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'base_tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/order_item'), 'tax_before_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote_item'), 'original_custom_price', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'subtotal', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_subtotal', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'subtotal_with_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_subtotal_with_discount', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'is_changed', 'int(10) unsigned');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_to_global_rate', 'decimal(12,4)');
$installer->getConnection()->modifyColumn($installer->getTable('sales/quote'), 'base_to_quote_rate', 'decimal(12,4)');
