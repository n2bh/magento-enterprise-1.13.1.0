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

/**
 * Customer balance controller for shopping cart
 *
 */
class Enterprise_CustomerBalance_CartController extends Mage_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Remove Store Credit from current quote
     *
     */
    public function removeAction()
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote->getUseCustomerBalance()) {
            Mage::getSingleton('checkout/session')->addSuccess(
                $this->__('The store credit payment has been removed from shopping cart.')
            );
            $quote->setUseCustomerBalance(false)->collectTotals()->save();
        } else {
            Mage::getSingleton('checkout/session')->addError(
                $this->__('Store Credit payment is not being used in your shopping cart.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
