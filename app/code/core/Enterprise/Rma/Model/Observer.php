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
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * RMA observer
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Observer
{
    /**
     * Add rma availability option to options column in customer's order grid
     *
     * @param Varien_Event_Observer $observer
     */
    public function addRmaOption(Varien_Event_Observer $observer)
    {
        $renderer = $observer->getEvent()->getRenderer();
        /** @var $row Mage_Sales_Model_Order */
        $row = $observer->getEvent()->getRow();

        if (Mage::helper('enterprise_rma')->canCreateRma($row, true)) {
            $reorderAction = array(
                    '@' =>  array('href' => $renderer->getUrl('*/rma/new', array('order_id'=>$row->getId()))),
                    '#' =>  Mage::helper('enterprise_rma')->__('Return')
            );
            $renderer->addToActions($reorderAction);
        }
    }
}
