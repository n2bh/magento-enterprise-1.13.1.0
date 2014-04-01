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


class Enterprise_GiftCardAccount_Model_Total_Quote_GiftCardAccount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Init total model, set total code
     */
    public function __construct()
    {
        $this->setCode('giftcardaccount');
    }

    /**
     * Collect giftcertificate totals for specified address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_collectQuoteGiftCards($address->getQuote());
        $baseAmountLeft = $address->getQuote()->getBaseGiftCardsAmount()
            - $address->getQuote()->getBaseGiftCardsAmountUsed();
        $amountLeft = $address->getQuote()->getGiftCardsAmount()-$address->getQuote()->getGiftCardsAmountUsed();

        $baseTotalUsed = $totalUsed = $baseUsed = $used = $skipped = $baseSaved = $saved = 0;

        if ($baseAmountLeft >= $address->getBaseGrandTotal()) {
            $baseUsed = $address->getBaseGrandTotal();
            $used = $address->getGrandTotal();

            $address->setBaseGrandTotal(0);
            $address->setGrandTotal(0);
        } else {
            $baseUsed = $baseAmountLeft;
            $used = $amountLeft;

            $address->setBaseGrandTotal($address->getBaseGrandTotal()-$baseAmountLeft);
            $address->setGrandTotal($address->getGrandTotal()-$amountLeft);
        }

        $addressCards = array();
        $usedAddressCards = array();
        if ($baseUsed) {
            $quoteCards = $this->_sortGiftCards(Mage::helper('enterprise_giftcardaccount')->getCards($address->getQuote()));
            foreach ($quoteCards as $quoteCard) {
                $card = $quoteCard;
                if ($quoteCard['ba'] + $skipped <= $address->getQuote()->getBaseGiftCardsAmountUsed()) {
                    $baseThisCardUsedAmount = $thisCardUsedAmount = 0;
                } elseif ($quoteCard['ba'] + $baseSaved > $baseUsed) {
                    $baseThisCardUsedAmount = min($quoteCard['ba'], $baseUsed-$baseSaved);
                    $thisCardUsedAmount = min($quoteCard['a'], $used-$saved);

                    $baseSaved += $baseThisCardUsedAmount;
                    $saved += $thisCardUsedAmount;
                } elseif ($quoteCard['ba'] + $skipped + $baseSaved > $address->getQuote()->getBaseGiftCardsAmountUsed()) {
                    $baseThisCardUsedAmount = min($quoteCard['ba'], $baseUsed);
                    $thisCardUsedAmount = min($quoteCard['a'], $used);

                    $baseSaved += $baseThisCardUsedAmount;
                    $saved += $thisCardUsedAmount;
                } else {
                    $baseThisCardUsedAmount = $thisCardUsedAmount = 0;
                }
                // avoid possible errors in future comparisons
                $card['ba'] = round($baseThisCardUsedAmount, 4);
                $card['a'] = round($thisCardUsedAmount, 4);
                $addressCards[] = $card;
                if ($baseThisCardUsedAmount) {
                    $usedAddressCards[] = $card;
                }

                $skipped += $quoteCard['ba'];
            }
        }
        Mage::helper('enterprise_giftcardaccount')->setCards($address, $usedAddressCards);
        $address->setUsedGiftCards($address->getGiftCards());
        Mage::helper('enterprise_giftcardaccount')->setCards($address, $addressCards);

        $baseTotalUsed = $address->getQuote()->getBaseGiftCardsAmountUsed() + $baseUsed;
        $totalUsed = $address->getQuote()->getGiftCardsAmountUsed() + $used;

        $address->getQuote()->setBaseGiftCardsAmountUsed($baseTotalUsed);
        $address->getQuote()->setGiftCardsAmountUsed($totalUsed);

        $address->setBaseGiftCardsAmount($baseUsed);
        $address->setGiftCardsAmount($used);

        return $this;
    }

    protected function _collectQuoteGiftCards($quote)
    {
        if (!$quote->getGiftCardsTotalCollected()) {
            $quote->setBaseGiftCardsAmount(0);
            $quote->setGiftCardsAmount(0);

            $quote->setBaseGiftCardsAmountUsed(0);
            $quote->setGiftCardsAmountUsed(0);

            $baseAmount = 0;
            $amount = 0;
            $cards = Mage::helper('enterprise_giftcardaccount')->getCards($quote);
            foreach ($cards as $k=>&$card) {
                $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')->load($card['i']);
                if ($model->isExpired() || $model->getBalance() == 0) {
                    unset($cards[$k]);
                } else if ($model->getBalance() != $card['ba']) {
                    $card['ba'] = $model->getBalance();
                } else {
                    $card['a'] = $quote->getStore()->roundPrice($quote->getStore()->convertPrice($card['ba']));
                    $baseAmount += $card['ba'];
                    $amount += $card['a'];
                }
            }
            Mage::helper('enterprise_giftcardaccount')->setCards($quote, $cards);

            $quote->setBaseGiftCardsAmount($baseAmount);
            $quote->setGiftCardsAmount($amount);

            $quote->setGiftCardsTotalCollected(true);
        }
    }

    /**
     * Return shopping cart total row items
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Enterprise_GiftCardAccount_Model_Total_Quote_GiftCardAccount
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getQuote()->isVirtual()) {
            $giftCards = Mage::helper('enterprise_giftcardaccount')->getCards($address->getQuote()->getBillingAddress());
        } else {
            $giftCards = Mage::helper('enterprise_giftcardaccount')->getCards($address);
        }
        $address->addTotal(array(
            'code'=>$this->getCode(),
            'title'=>Mage::helper('enterprise_giftcardaccount')->__('Gift Cards'),
            'value'=>-$address->getGiftCardsAmount(),
            'gift_cards'=>$giftCards,
        ));

        return $this;
    }

    protected function _sortGiftCards($in)
    {
        usort($in, array($this, 'compareGiftCards'));
        return $in;
    }

    public static function compareGiftCards($a, $b)
    {
        if ($a['ba'] == $b['ba']) {
            return 0;
        }
        return ($a['ba'] > $b['ba']) ? 1 : -1;
    }
}
