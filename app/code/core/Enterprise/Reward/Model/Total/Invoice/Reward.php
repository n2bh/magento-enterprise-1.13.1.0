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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward sales order invoice total model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Total_Invoice_Reward extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect reward total for invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Enterprise_Reward_Model_Total_Invoice_Reward
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $rewardCurrecnyAmountLeft = $order->getRewardCurrencyAmount() - $order->getRewardCurrencyAmountInvoiced();
        $baseRewardCurrecnyAmountLeft = $order->getBaseRewardCurrencyAmount() - $order->getBaseRewardCurrencyAmountInvoiced();
        if ($order->getBaseRewardCurrencyAmount() && $baseRewardCurrecnyAmountLeft > 0) {
            if ($baseRewardCurrecnyAmountLeft < $invoice->getBaseGrandTotal()) {
                $invoice->setGrandTotal($invoice->getGrandTotal() - $rewardCurrecnyAmountLeft);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseRewardCurrecnyAmountLeft);
            } else {
                $rewardCurrecnyAmountLeft = $invoice->getGrandTotal();
                $baseRewardCurrecnyAmountLeft = $invoice->getBaseGrandTotal();

                $invoice->setGrandTotal(0);
                $invoice->setBaseGrandTotal(0);
            }
            $pointValue = $order->getRewardPointsBalance() / $order->getBaseRewardCurrencyAmount();
            $rewardPointsBalance = $baseRewardCurrecnyAmountLeft*ceil($pointValue);
            $rewardPointsBalanceLeft = $order->getRewardPointsBalance() - $order->getRewardPointsBalanceInvoiced();
            if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
                $rewardPointsBalance = $rewardPointsBalanceLeft;
            }
            $invoice->setRewardPointsBalance($rewardPointsBalance);
            $invoice->setRewardCurrencyAmount($rewardCurrecnyAmountLeft);
            $invoice->setBaseRewardCurrencyAmount($baseRewardCurrecnyAmountLeft);
        }
        return $this;
    }
}
