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
 * Reward history container
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('enterprise/reward/customer/edit/history.phtml');
    }

    /**
     * Prepare layout.
     * Create history grid block
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
     */
    protected function _prepareLayout()
    {
        $grid = $this->getLayout()
            ->createBlock('enterprise_reward/adminhtml_customer_edit_tab_reward_history_grid')
            ->setCustomerId($this->getCustomerId());
        $this->setChild('grid', $grid);
        return parent::_prepareLayout();
    }
}
