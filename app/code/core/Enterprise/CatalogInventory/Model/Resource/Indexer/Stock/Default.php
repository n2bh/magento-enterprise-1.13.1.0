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
 * @package     Enterprise_CatalogInventory
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Default Stock Status Indexer Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogInventory_Model_Resource_Indexer_Stock_Default
    extends Mage_CatalogInventory_Model_Resource_Indexer_Stock_Default
{
    /**
     * Reindex all stock status data for default logic product type
     *
     * @return Enterprise_CatalogInventory_Model_Resource_Indexer_Stock_Default
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->_prepareIndexTable();
        return $this;
    }
}
