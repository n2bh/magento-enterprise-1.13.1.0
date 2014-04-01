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
 * @package     Enterprise_UrlRewrite
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * UrlRewrite resource model
 *
 * @category    Enterprise
 * @package     Enterprise_UrlRewrite
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_UrlRewrite_Model_Resource_Url_Rewrite extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('enterprise_urlrewrite/url_rewrite', 'url_rewrite_id');
    }

    /**
     * Get rewrite rows by paths
     *
     * @param array $paths
     * @return array
     */
    public function getRewrites($paths)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()))
            ->where('m.request_path IN (?) ', $paths)
            ->order('m.store_id DESC');
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Load rewrite by request_path value
     *
     * @param Mage_Core_Model_Abstract $object
     * @param array $paths
     * @return Enterprise_UrlRewrite_Model_Resource_Url_Rewrite
     */
    public function loadByRequestPath(Mage_Core_Model_Abstract $object, $paths)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), array(new Zend_Db_Expr('COUNT(url_rewrite_id)')))
            ->where('m.request_path in (?)', $paths);

        $result = $this->_getReadAdapter()->fetchOne($select);

        if (count($paths) == (int)$result) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('m' => $this->getMainTable()))
                ->where('m.request_path = ?', array_pop($paths));

            $result = $this->_getReadAdapter()->fetchRow($select);

            if ($result) {
                $object->setData($result);
            }

            $this->unserializeFields($object);
            $this->_afterLoad($object);
        }

        return $this;
    }
}
