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
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/** @var $helper Enterprise_Customer_Helper_Data */
$helper = Mage::helper('enterprise_customer');
$customerAttributes = $helper->getCustomerAttributeFormOptions();

$attributes = array(
    'dob',
    'gender',
    'taxvat',
);

foreach ($attributes as $attributeCode) {
    $attribute      = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
    $getUsedInForms = $attribute->getUsedInForms();
    $usedInForms    = array();
    foreach($customerAttributes as $customerAttribute) {
        if (!in_array($customerAttribute['value'], $getUsedInForms)) {
            $usedInForms[] = $customerAttribute['value'];
        }
    }
    if (!empty($usedInForms)) {
        $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
}
