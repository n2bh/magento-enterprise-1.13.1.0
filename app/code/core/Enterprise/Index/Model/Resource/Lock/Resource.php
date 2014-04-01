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
 * Lock resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Index_Model_Resource_Lock_Resource extends Mage_Core_Model_Resource
{
    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @param string $extendConfigWith
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function getConnection($name, $extendConfigWith = '')
    {
        $index = $name . $extendConfigWith;
        if (isset($this->_connections[$index])) {
            $connection = $this->_connections[$index];
            if (isset($this->_skippedConnections[$index]) && !Mage::app()->getIsCacheLocked()) {
                $connection->setCacheAdapter(Mage::app()->getCache());
                unset($this->_skippedConnections[$index]);
            }
            return $connection;
        }
        $connConfig = Mage::getConfig()->getResourceConnectionConfig($name);

        if (!$connConfig) {
            $this->_connections[$index] = $this->_getDefaultConnection($name, $extendConfigWith);
            return $this->_connections[$index];
        }
        if (!$connConfig->is('active', 1)) {
            return false;
        }

        $origName = $connConfig->getParent()->getName() . $extendConfigWith;
        if (isset($this->_connections[$origName])) {
            $this->_connections[$index] = $this->_connections[$origName];
            return $this->_connections[$origName];
        }

        if ($extendConfigWith) {
            $connConfig->extend(Mage::getConfig()->getResourceConnectionConfig($extendConfigWith), true);
        }

        $connection = $this->_newConnection((string)$connConfig->type, $connConfig);
        if ($connection) {
            if (Mage::app()->getIsCacheLocked()) {
                $this->_skippedConnections[$index] = true;
            } else {
                $connection->setCacheAdapter(Mage::app()->getCache());
            }
        }

        $this->_connections[$index] = $connection;
        if ($origName !== $index) {
            $this->_connections[$origName] = $connection;
        }

        return $connection;
    }

    /**
     * Retrieve default connection name by required connection name
     *
     * @param string $requiredConnectionName
     * @param string $extendConfigWith
     *
     * @return string
     */
    protected function _getDefaultConnection($requiredConnectionName, $extendConfigWith = '')
    {
        if (strpos($requiredConnectionName, 'read') !== false) {
            return $this->getConnection(self::DEFAULT_READ_RESOURCE, $extendConfigWith);
        }
        return $this->getConnection(self::DEFAULT_WRITE_RESOURCE, $extendConfigWith);
    }
}
