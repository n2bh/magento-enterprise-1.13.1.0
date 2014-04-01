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
 * @package     Enterprise_GiftWrapping
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftWrapping_Model_Sales_Pdf_Items_Creditmemo_Giftwrapping
    extends Enterprise_GiftWrapping_Model_Sales_Pdf_Items_Abstract
{
    /**
     * Prepare item lines
     *
     * @return array
     */
    protected function _prepareLines()
    {
        $lines = array();
        $order = $this->getOrder();
        $item  = $this->getItem();

        $lines[] = array(
            array(
                'text' => Mage::helper('enterprise_giftwrapping')->__('Gift Wrapping'),
                'feed' => 35
            ),
            array(
                'text' => $order->formatPriceTxt($item->getGwPrice()),
                'feed' => 330,
                'font' => 'bold',
                'align' => 'right'
            ),
            array(
                'text' => $order->formatPriceTxt($item->getGwTaxAmount()),
                'feed' => 495,
                'font' => 'bold',
                'align' => 'right'
            ),
            array(
                'text' => $order->formatPriceTxt($item->getGwPrice() + $item->getGwTaxAmount()),
                'feed' => 565,
                'font' => 'bold',
                'align' => 'right'
            )
        );

        return $lines;
    }
}
