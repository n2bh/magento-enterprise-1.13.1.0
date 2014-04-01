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
 * Reward Points Settings form
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_Reward_Subscription extends Mage_Core_Block_Template
{
    /**
     * Getter for RewardUpdateNotification
     *
     * @return bool
     */
    public function isSubscribedForUpdates()
    {
        return (bool)$this->_getCustomer()->getRewardUpdateNotification();
    }

    /**
     * Getter for RewardWarningNotification
     *
     * @return bool
     */
    public function isSubscribedForWarnings()
    {
        return (bool)$this->_getCustomer()->getRewardWarningNotification();
    }

    /**
     * Retrieve customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
