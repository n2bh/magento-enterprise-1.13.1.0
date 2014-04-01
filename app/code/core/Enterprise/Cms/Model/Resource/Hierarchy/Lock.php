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
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Hierarchy Lock Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @deprecated since 1.12.0.0
 */
class Enterprise_Cms_Model_Resource_Hierarchy_Lock extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy_lock', 'lock_id');
    }

    /**
     * Return last lock information
     *
     * @return array
     */
    public function getLockData()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->order('lock_id ' . Varien_Db_Select::SQL_DESC)
            ->limit(1);
        $data = $this->_getReadAdapter()->fetchRow($select);
        return is_array($data) ? $data : array();
    }
}
