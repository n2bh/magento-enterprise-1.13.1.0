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
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Admin Checkout block for returning dynamically loaded content
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Load extends Mage_Core_Block_Template
{
    /*
     * Returns string text with response of loading some blocks
     *
     * @return string
     */
    protected function _toHtml()
    {
        $result = array();
        foreach ($this->getSortedChildren() as $name) {
            $block = $this->getChild($name);
            if (!$block) {
                $result[$name] = $this->__('Invalid block: %s.', $name);
            } else {
                $result[$name] = $block->toHtml();
            }
        }
        $resultJson = Mage::helper('core')->jsonEncode($result);
        $jsVarname = $this->getRequest()->getParam('as_js_varname');
        if ($jsVarname) {
            return Mage::helper('adminhtml/js')->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
        } else {
            return $resultJson;
        }
    }
}
