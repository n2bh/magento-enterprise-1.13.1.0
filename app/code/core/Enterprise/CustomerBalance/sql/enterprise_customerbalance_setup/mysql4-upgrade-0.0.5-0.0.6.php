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
 * @package     Enterprise_CustomerBalance
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('quote', 'customer_balance_amount_used', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_customer_balance_amount_used', array('type'=>'decimal'));

$installer->addAttribute('quote_address', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'customer_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'base_customer_balance_invoiced', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_balance_invoiced', array('type'=>'decimal'));

$installer->addAttribute('order', 'base_customer_balance_refunded', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_balance_refunded', array('type'=>'decimal'));

$installer->addAttribute('invoice', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'customer_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'customer_balance_amount', array('type'=>'decimal'));

$installer->endSetup();
