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
 * @package     Enterprise_GiftCardAccount
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional extends Mage_Core_Block_Template
{
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getAppliedGiftCardAmount()
    {
        return $this->_getQuote()->getBaseGiftCardsAmountUsed();
    }

    public function isFullyPaidAfterApplication()
    {
        // TODO remove dependences to other modules
        if ($this->_getQuote()->getBaseGrandTotal() > 0 || $this->_getQuote()->getCustomerBalanceAmountUsed() > 0 || $this->_getQuote()->getRewardPointsBalance() > 0) {
            return false;
        }

        return true;
    }
}
