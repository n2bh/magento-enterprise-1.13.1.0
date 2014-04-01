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
 * Lock model
 *
 * @category    Enterprise
 * @package     Enterprise_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Index_Model_Lock
{
    /**
     * Lock storage config path
     */
    const STORAGE_CONFIG_PATH = 'global/index/lock/storage';

    /**
     * Storage instance
     *
     * @var Enterprise_Index_Model_Lock_Storage_Interface
     */
    protected $_storage;

    /**
     * Singleton instance
     *
     * @var Enterprise_Index_Model_Lock
     */
    protected static $_instance;

    /**
     * Array of registered locks
     *
     * @var array
     */
    protected static $_locks = array();

    /**
     * Constructor
     */
    protected function __construct()
    {
        register_shutdown_function(array($this, 'shutdownReleaseLocks'));
    }

    /**
     * Get lock singleton instance
     *
     * @return Enterprise_Index_Model_Lock
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Release all locks on application shutdown
     */
    public function shutdownReleaseLocks()
    {
        foreach (self::$_locks as $lock) {
            $this->releaseLock($lock);
        }
    }

    /**
     * Set named lock
     *
     * @param string $lockName
     * @return int
     */
    public function setLock($lockName)
    {
        if ($this->_getStorage()->setLock($lockName)) {
            self::$_locks[$lockName] = $lockName;
            return true;
        }
        return false;
    }

    /**
     * Release named lock by name
     *
     * @param string $lockName
     * @return int|null
     */
    public function releaseLock($lockName)
    {
        if ($this->_getStorage()->releaseLock($lockName)) {
            unset(self::$_locks[$lockName]);
            return true;
        }
        return false;
    }

    /**
     * Check whether the named lock exists
     *
     * @param string $lockName
     * @return bool
     */
    public function isLockExists($lockName)
    {
        return (bool) $this->_getStorage()->isLockExists($lockName);
    }

    /**
     * Get lock storage model
     *
     * @return Enterprise_Index_Model_Lock_Storage_Interface
     */
    protected function _getStorage()
    {
        if (!$this->_storage instanceof Enterprise_Index_Model_Lock_Storage_Interface) {
            $config = Mage::getConfig()->getNode(self::STORAGE_CONFIG_PATH);
            $this->_storage = Mage::getModel($config->model);
        }
        return $this->_storage;
    }
}
