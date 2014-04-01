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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
$tableName = $installer->getTable('catalog/product_index_eav_decimal');
$indexName = $installer->getConnection()->getPrimaryKeyName($tableName);


$tableNameTmp = $installer->getTable('catalog/product_eav_decimal_indexer_tmp');
$indexNameTmp = $installer->getConnection()->getPrimaryKeyName($tableNameTmp);

$fields = array('entity_id', 'attribute_id', 'store_id');

$installer->getConnection()
        ->addIndex($tableName, $indexName, $fields, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
        ->addIndex($tableNameTmp, $indexNameTmp, $fields, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

