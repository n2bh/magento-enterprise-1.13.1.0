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
 * @package     Enterprise_GiftCard
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * GiftCard product view form
 *
 * @category   Enterprise
 * @package    Enterprise_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCard_Block_Catalog_Product_View_Type_Giftcard_Form extends Mage_Core_Block_Template
{
    /**
     * Check if email is available
     *
     * @return bool
     */
    public function isEmailAvailable()
    {
        $emailNeeded = $this->getEmailNeeded();
        if ($emailNeeded === null) {
            $product = Mage::registry('current_product');
            if (!$product) {
                return false;
            }
            $emailNeeded = !$product->getTypeInstance()->isTypePhysical();
            $this->setEmailNeeded($emailNeeded);
        }
        return (bool)$emailNeeded;
    }

    /**
     * Get customer name from session
     *
     * @return string
     */
    public function getCustomerName()
    {
        $firstName = (string)Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
        $lastName  = (string)Mage::getSingleton('customer/session')->getCustomer()->getLastname();

        if ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        } else {
            return '';
        }
    }

    /**
     * Get customer email from session
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return (string) Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }
}
