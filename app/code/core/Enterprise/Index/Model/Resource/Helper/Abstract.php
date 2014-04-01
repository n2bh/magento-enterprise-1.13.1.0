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
 * @package     Enterprise_Index
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Abstract resource helper
 *
 * @category    Enterprise
 * @package     Enterprise_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Index_Model_Resource_Helper_Abstract
{
    /**
     * Write adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_writeAdapter;

    /**
     * Timeout for lock get proc.
     */
    const LOCK_GET_TIMEOUT = 5;

    /**
     * Set lock
     *
     * @param string $name
     * @return int
     */
    abstract public function setLock($name);

    /**
     * Release lock
     *
     * @param string $name
     * @return int
     */
    abstract public function releaseLock($name);

    /**
     * Is lock exists
     *
     * @param string $name
     * @return bool
     */
    abstract public function isLocked($name);

    /**
     * @param Varien_Db_Adapter_Interface $adapter
     * @return $this
     */
    public function setWriteAdapter(Varien_Db_Adapter_Interface $adapter)
    {
        $this->_writeAdapter = $adapter;

        return $this;
    }

    /**
     * Returns write adapter instance
     *
     * @throws Enterprise_Index_Exception
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if (null === $this->_writeAdapter) {
            throw new Enterprise_Index_Exception('Write adapter has to be previously set.');
        }

        return $this->_writeAdapter;
    }
}
