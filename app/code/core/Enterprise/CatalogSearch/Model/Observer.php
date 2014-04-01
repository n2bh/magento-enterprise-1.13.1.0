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
 * @package     Enterprise_CatalogSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Enterprise index observer
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogSearch_Model_Observer
{
    /**
     * Path to fulltext indexer mode
     */
    const XML_PATH_LIVE_FULLTEXT_REINDEX_ENABLED = 'index_management/index_options/fulltext';

    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Constructor with parameters
     * Array of arguments with keys
     *  - 'factory' Mage_Core_Model_Factory
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_store = !empty($args['store']) ? $args['store'] : Mage::app()->getStore();
    }

    /**
     * Process fulltext refresh upon product save event
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogSearch_Model_Observer
     */
    public function processProductSaveEvent(Varien_Event_Observer $observer)
    {
        if (!$this->_isLiveFulltextReindexEnabled()) {
            return $this;
        }
        //Fulltext refresh
        $client = $this->_getClient('catalogsearch_fulltext');
        $client->execute('enterprise_catalogsearch/index_action_fulltext_refresh_row', array(
            'value' => $observer->getEvent()->getProduct()->getId(),
        ));

        return $this;
    }

    /**
     * Retrieves fulltext indexer mode
     *
     * @return boolean
     */
    protected function _isLiveFulltextReindexEnabled()
    {
        return (bool)(string)$this->_store->getConfig(self::XML_PATH_LIVE_FULLTEXT_REINDEX_ENABLED);
    }

    /**
     * Process shell reindex catalog full text refresh event
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogSearch_Model_Observer
     */
    public function processShellFulltextReindexEvent(Varien_Event_Observer $observer)
    {
        $client = $this->_getClient('catalogsearch_fulltext');
        $client->execute('enterprise_catalogsearch/index_action_fulltext_refresh');

        return $this;
    }

    /**
     * Get client
     *
     * @param string $metadataTableName
     * @return Enterprise_Mview_Model_Client
     */
    protected function _getClient($metadataTableName)
    {
        /** @var $client Enterprise_Mview_Model_Client */
        $client = $this->_factory->getModel('enterprise_mview/client', array(array('factory' => $this->_factory)));
        $client->init($metadataTableName);
        return $client;
    }

    /**
     * Exclude catalogsearch_fulltext indexer
     *
     * @param Varien_Event_Observer $observer
     */
    public function addExcludeProcess(Varien_Event_Observer $observer)
    {
        $helper = $this->_factory->getHelper('enterprise_catalogsearch');
        if (!$helper->isFulltextOn()) {
            $observer->getEvent()->getCollection()
                ->addExcludeProcessByCode('catalogsearch_fulltext');
        }
    }
}
