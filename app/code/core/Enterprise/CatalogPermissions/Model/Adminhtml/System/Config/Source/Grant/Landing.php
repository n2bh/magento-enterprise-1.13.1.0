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
 * @package     Enterprise_CatalogPermissions
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Configuration source for grant permission select
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Adminhtml_System_Config_Source_Grant_Landing
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Enterprise_CatalogPermissions_Helper_Data::GRANT_ALL            => Mage::helper('enterprise_catalogpermissions')->__('Yes, for Everyone'),
            Enterprise_CatalogPermissions_Helper_Data::GRANT_CUSTOMER_GROUP => Mage::helper('enterprise_catalogpermissions')->__('Yes, for Specified Customer Groups'),
            Enterprise_CatalogPermissions_Helper_Data::GRANT_NONE           => Mage::helper('enterprise_catalogpermissions')->__('No, Redirect to Landing Page')
        );
    }
}
