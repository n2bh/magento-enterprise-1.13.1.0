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

class Enterprise_GiftCard_Model_Observer extends Mage_Core_Model_Abstract
{
    const ATTRIBUTE_CODE = 'giftcard_amounts';

    /**
     * Set attribute renderer on catalog product edit page
     *
     * @param Varien_Event_Observer $observer
     */
    public function setAmountsRendererInForm(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getEvent()->getForm();
        $elem = $form->getElement(self::ATTRIBUTE_CODE);

        if ($elem) {
            $elem->setRenderer(Mage::app()->getLayout()->createBlock('enterprise_giftcard/adminhtml_renderer_amount'));
        }
    }

    /**
     * Set giftcard amounts field as not used in mass update
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateExcludedFieldList(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $list[] = self::ATTRIBUTE_CODE;
        $block->setFormExcludedFieldList($list);
    }

    /**
     * Append gift card additional data to order item options
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCard_Model_Observer
     */
    public function appendGiftcardAdditionalData(Varien_Event_Observer $observer)
    {
        //sales_convert_quote_item_to_order_item

        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getItem();
        $keys = array(
            'giftcard_sender_name',
            'giftcard_sender_email',
            'giftcard_recipient_name',
            'giftcard_recipient_email',
            'giftcard_message',
        );
        $productOptions = $orderItem->getProductOptions();
        foreach ($keys as $key) {
            if ($option = $quoteItem->getProduct()->getCustomOption($key)) {
                $productOptions[$key] = $option->getValue();
            }
        }

        $product = $quoteItem->getProduct();
        // set lifetime
        $lifetime = 0;
        if ($product->getUseConfigLifetime()) {
            $lifetime = Mage::getStoreConfig(
                Enterprise_GiftCard_Model_Giftcard::XML_PATH_LIFETIME,
                $orderItem->getStore()
            );
        } else {
            $lifetime = $product->getLifetime();
        }
        $productOptions['giftcard_lifetime'] = $lifetime;

        // set is_redeemable
        $isRedeemable = 0;
        if ($product->getUseConfigIsRedeemable()) {
            $isRedeemable = Mage::getStoreConfigFlag(
                Enterprise_GiftCard_Model_Giftcard::XML_PATH_IS_REDEEMABLE,
                $orderItem->getStore()
            );
        } else {
            $isRedeemable = (int) $product->getIsRedeemable();
        }
        $productOptions['giftcard_is_redeemable'] = $isRedeemable;

        // set email_template
        $emailTemplate = 0;
        if ($product->getUseConfigEmailTemplate()) {
            $emailTemplate = Mage::getStoreConfig(
                Enterprise_GiftCard_Model_Giftcard::XML_PATH_EMAIL_TEMPLATE,
                $orderItem->getStore()
            );
        } else {
            $emailTemplate = $product->getEmailTemplate();
        }
        $productOptions['giftcard_email_template'] = $emailTemplate;
        $productOptions['giftcard_type'] = $product->getGiftcardType();

        $orderItem->setProductOptions($productOptions);

        return $this;
    }

    /**
     * Generate gift card accounts after order save
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCard_Model_Observer
     */
    public function generateGiftCardAccounts(Varien_Event_Observer $observer)
    {
        // sales_order_save_after

        $order = $observer->getEvent()->getOrder();
        $requiredStatus = Mage::getStoreConfig(
            Enterprise_GiftCard_Model_Giftcard::XML_PATH_ORDER_ITEM_STATUS,
            $order->getStore());
        $loadedInvoices = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD) {
                $qty = 0;
                $options = $item->getProductOptions();

                switch ($requiredStatus) {
                    case Mage_Sales_Model_Order_Item::STATUS_INVOICED:
                        $paidInvoiceItems = (isset($options['giftcard_paid_invoice_items'])
                            ? $options['giftcard_paid_invoice_items']
                            : array());
                        // find invoice for this order item
                        $invoiceItemCollection = Mage::getResourceModel('sales/order_invoice_item_collection')
                            ->addFieldToFilter('order_item_id', $item->getId());

                        foreach ($invoiceItemCollection as $invoiceItem) {
                            $invoiceId = $invoiceItem->getParentId();
                            if(isset($loadedInvoices[$invoiceId])) {
                                $invoice = $loadedInvoices[$invoiceId];
                            } else {
                                $invoice = Mage::getModel('sales/order_invoice')
                                    ->load($invoiceId);
                                $loadedInvoices[$invoiceId] = $invoice;
                            }
                            // check, if this order item has been paid
                            if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID &&
                                !in_array($invoiceItem->getId(), $paidInvoiceItems)
                            ) {
                                    $qty += $invoiceItem->getQty();
                                    $paidInvoiceItems[] = $invoiceItem->getId();
                            }
                        }
                        $options['giftcard_paid_invoice_items'] = $paidInvoiceItems;
                        break;
                    default:
                        $qty = $item->getQtyOrdered();
                        if (isset($options['giftcard_created_codes'])) {
                            $qty -= count($options['giftcard_created_codes']);
                        }
                        break;
                }

                $hasFailedCodes = false;
                if ($qty > 0) {
                    $isRedeemable = 0;
                    if ($option = $item->getProductOptionByCode('giftcard_is_redeemable')) {
                        $isRedeemable = $option;
                    }

                    $lifetime = 0;
                    if ($option = $item->getProductOptionByCode('giftcard_lifetime')) {
                        $lifetime = $option;
                    }

                    $amount = $item->getBasePrice();
                    $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();

                    $data = new Varien_Object();
                    $data->setWebsiteId($websiteId)
                        ->setAmount($amount)
                        ->setLifetime($lifetime)
                        ->setIsRedeemable($isRedeemable)
                        ->setOrderItem($item);

                    $codes = (isset($options['giftcard_created_codes']) ?
                        $options['giftcard_created_codes'] : array());
                    $goodCodes = 0;
                    for ($i = 0; $i < $qty; $i++) {
                        try {
                            $code = new Varien_Object();
                            Mage::dispatchEvent('enterprise_giftcardaccount_create',
                                array('request'=>$data, 'code'=>$code));
                            $codes[] = $code->getCode();
                            $goodCodes++;
                        } catch (Mage_Core_Exception $e) {
                            $hasFailedCodes = true;
                            $codes[] = null;
                        }
                    }
                    if ($goodCodes && $item->getProductOptionByCode('giftcard_recipient_email')) {
                        $sender = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderName = $item->getProductOptionByCode('giftcard_sender_name');
                        if ($senderEmail = $item->getProductOptionByCode('giftcard_sender_email')) {
                            $sender = "$sender <$senderEmail>";
                        }

                        $codeList = Mage::helper('enterprise_giftcard')->getEmailGeneratedItemsBlock()
                            ->setCodes($codes)
                            ->setIsRedeemable($isRedeemable)
                            ->setStore(Mage::app()->getStore($order->getStoreId()));
                        $balance = Mage::app()->getLocale()->currency(
                            Mage::app()->getStore($order->getStoreId())
                            ->getBaseCurrencyCode())->toCurrency($amount);

                        $templateData = array(
                            'name'                   => $item->getProductOptionByCode('giftcard_recipient_name'),
                            'email'                  => $item->getProductOptionByCode('giftcard_recipient_email'),
                            'sender_name_with_email' => $sender,
                            'sender_name'            => $senderName,
                            'gift_message'           => $item->getProductOptionByCode('giftcard_message'),
                            'giftcards'              => $codeList->toHtml(),
                            'balance'                => $balance,
                            'is_multiple_codes'      => 1 < $goodCodes,
                            'store'                  => $order->getStore(),
                            'store_name'             => $order->getStore()->getName(),//@deprecated after 1.4.0.0-beta1
                            'is_redeemable'          => $isRedeemable,
                        );

                        $email = Mage::getModel('core/email_template')
                            ->setDesignConfig(array('store' => $item->getOrder()->getStoreId()));
                        $email->sendTransactional(
                            $item->getProductOptionByCode('giftcard_email_template'),
                            Mage::getStoreConfig(
                                Enterprise_GiftCard_Model_Giftcard::XML_PATH_EMAIL_IDENTITY,
                                $item->getOrder()->getStoreId()),
                            $item->getProductOptionByCode('giftcard_recipient_email'),
                            $item->getProductOptionByCode('giftcard_recipient_name'),
                            $templateData
                        );

                        if ($email->getSentSuccess()) {
                            $options['email_sent'] = 1;
                        }
                    }
                    $options['giftcard_created_codes'] = $codes;
                    $item->setProductOptions($options);
                    $item->save();
                }
                if ($hasFailedCodes) {
                    $url = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/giftcardaccount');
                    $message = Mage::helper('enterprise_giftcard')->__('Some of Gift Card Accounts were not generated properly. You can create Gift Card Accounts manually <a href="%s">here</a>.', $url);

                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
            }
        }

        return $this;
    }

    /**
     * Process `giftcard_amounts` attribute afterLoad logic on loading by collection
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCard_Model_Observer
     */
    public function loadAttributesAfterCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();

        foreach ($collection as $item) {
            if (Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD == $item->getTypeId()) {
                $attribute = $item->getResource()->getAttribute('giftcard_amounts');
                if ($attribute->getId()) {
                    $attribute->getBackend()->afterLoad($item);
                }
            }
        }
        return $this;
    }

    /**
     * Initialize product options renderer with giftcard specific params
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCard_Model_Observer
     */
    public function initOptionRenderer(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('giftcard', 'enterprise_giftcard/catalog_product_configuration');
        return $this;
    }
}
