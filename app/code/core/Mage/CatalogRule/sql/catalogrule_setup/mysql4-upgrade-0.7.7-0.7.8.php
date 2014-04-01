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
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$ruleGroupWebsiteTable = $installer->getTable('catalogrule/rule_group_website');

$installer->run("CREATE TABLE `{$ruleGroupWebsiteTable}` (
 `rule_id` int(10) unsigned NOT NULL default '0',
 `customer_group_id` smallint(5) unsigned default NULL,
 `website_id` smallint(5) unsigned default NULL,
 KEY `rule_id` (`rule_id`),
 KEY `customer_group_id` (`customer_group_id`),
 KEY `website_id` (`website_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin");

$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_GROUP_WEBSITE_RULE', $ruleGroupWebsiteTable, 'rule_id',
    $installer->getTable('catalogrule/rule'), 'rule_id', 'CASCADE', 'CASCADE'
);
$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_GROUP_WEBSITE_GROUP', $ruleGroupWebsiteTable, 'customer_group_id',
    $installer->getTable('customer/customer_group'), 'customer_group_id', 'CASCADE', 'CASCADE'
);
$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_GROUP_WEBSITE_WEBSITE', $ruleGroupWebsiteTable, 'website_id',
    $installer->getTable('core/website'), 'website_id', 'CASCADE', 'CASCADE'
);

$installer->run("ALTER TABLE `{$ruleGroupWebsiteTable}` ADD PRIMARY KEY ( `rule_id` , `customer_group_id`, `website_id` )");
