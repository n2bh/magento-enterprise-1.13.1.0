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
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Enterprise_CustomerSegment_Model_Mysql4_Setup */

$installer->run("CREATE TABLE `{$this->getTable('enterprise_customersegment_segment')}` (
        `segment_id` int(10) unsigned NOT NULL auto_increment,
        `name` varchar(255) NOT NULL default '',
        `description` text NOT NULL,
        `is_active` tinyint(1) NOT NULL default '0',
        `conditions_serialized` mediumtext NOT NULL,
        `processing_frequency` int(100) NOT NULL,
    PRIMARY KEY  (`segment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
