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

/* @var $installer Enterprise_Reward_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$columnsToMove = array(
    'reward_update_notification',
    'reward_warning_notification'
);

foreach ($columnsToMove as $column) {
    $installer->addAttribute('customer', $column,
        array('type' => 'int', 'visible' => 0, 'visible_on_front' => 1)
    );
    $installer->getConnection()->dropColumn(
        $installer->getTable('enterprise_reward'), $column
    );
}

$installer->endSetup();
