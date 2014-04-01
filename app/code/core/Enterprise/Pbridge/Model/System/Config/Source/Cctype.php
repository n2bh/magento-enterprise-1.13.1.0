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
 * @package     Enterprise_Pbridge
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Pbridge_Model_System_Config_Source_Cctype extends Varien_Object
{
    /**
     * Return allowed cc types for current method
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        $configPathCcTypesAll = $this->getPath() . '_all';
        $ccTypes = Mage::getStoreConfig($configPathCcTypesAll);
        $ccTypes = explode(',', $ccTypes);
        $ccTypes = array_map('trim', $ccTypes);

        return $ccTypes;
    }

    /**
     * Return list of supported CC type codes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $model = Mage::getModel('payment/source_cctype')->setAllowedTypes($this->getAllowedTypes());
        return $model->toOptionArray();
    }
}
