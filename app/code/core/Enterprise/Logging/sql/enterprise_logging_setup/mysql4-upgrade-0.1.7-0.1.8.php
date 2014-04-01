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
 * @package     Enterprise_Logging
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$tableLog = $this->getTable('enterprise_logging/event');
$tableUser = $this->getTable('admin/user');

$installer->getConnection()->dropColumn($tableLog, 'user_id');
$installer->getConnection()->addColumn($tableLog, 'user_id', "mediumint(9) unsigned NULL DEFAULT NULL AFTER `user`");
$installer->getConnection()->addConstraint('FK_LOGGING_EVENT_USER', $tableLog, 'user_id', $tableUser, 'user_id', 'SET NULL');

$installer->endSetup();
