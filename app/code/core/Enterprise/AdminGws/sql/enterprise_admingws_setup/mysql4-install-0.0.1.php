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
 * @package     Enterprise_AdminGws
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Resource setup - add columns to roles table:
 * gws_is_all       - yes/no flag
 * gws_websites     - comma-separated
 * gws_store_groups - comma-separated
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$tableRoles = $installer->getTable('admin/role');

$installer->getConnection()->addColumn($tableRoles, 'gws_is_all', "tinyint(1) NOT NULL DEFAULT 1");
$installer->getConnection()->addColumn($tableRoles, 'gws_websites', "varchar(255) NOT NULL DEFAULT ''");
$installer->getConnection()->addColumn($tableRoles, 'gws_store_groups', "varchar(255) NOT NULL DEFAULT ''");

$installer->endSetup();
