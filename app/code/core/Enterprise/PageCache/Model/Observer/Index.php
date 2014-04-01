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
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Indexers FPC observer
 *
 * @category   Enterprise
 * @package    Enterprise_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Model_Observer_Index
{
    /**
     * Clean cache by specified entity and its ids
     *
     * @param Mage_Core_Model_Abstract $entity
     * @param array $ids
     */
    protected function _cleanEntityCache(Mage_Core_Model_Abstract $entity, array $ids)
    {
        $cacheTags = array();
        foreach ($ids as $entityId) {
            $entity->setId($entityId);
            $cacheTags = array_merge($cacheTags, $entity->getCacheIdTags());
        }
        if (!empty($cacheTags)) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean($cacheTags);
        }
    }

    /**
     * Invalidate FPC after full reindex
     *
     * @param Varien_Event_Observer $observer
     */
    public function invalidateCacheAfterFullReindex(Varien_Event_Observer $observer)
    {
        Mage::app()->getCacheInstance()->invalidateType('full_page');
    }

    /**
     * Clean cache for affected products
     *
     * @param Varien_Event_Observer $observer
     */
    public function cleanProductsCacheAfterPartialReindex(Varien_Event_Observer $observer)
    {
        $entityIds = $observer->getEvent()->getProductIds();
        if (is_array($entityIds)) {
            $this->_cleanEntityCache(Mage::getModel('catalog/product'), $entityIds);
        }
    }

    /**
     * Clean cache for affected categories
     *
     * @param Varien_Event_Observer $observer
     */
    public function cleanCategoriesCacheAfterPartialReindex(Varien_Event_Observer $observer)
    {
        $entityIds = $observer->getEvent()->getCategoryIds();
        if (is_array($entityIds)) {
            $this->_cleanEntityCache(Mage::getModel('catalog/category'), $entityIds);
        }
    }

    /**
     * Cleans cache by tags
     *
     * @param Varien_Event_Observer $observer
     */
    public function cleanCacheByTags(Varien_Event_Observer $observer)
    {
        $tags = $observer->getEvent()->getTags();

        if (!empty($tags)) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean($tags);
        }
    }

    /**
     * Clear request path cache by tag
     * (used for redirects invalidation)
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function clearRequestCacheByTag(Varien_Event_Observer $observer)
    {
        $redirects = $observer->getEvent()->getRedirects();
        foreach ($redirects as $redirect) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(
                array(
                    Enterprise_PageCache_Helper_Url::prepareRequestPathTag($redirect['identifier']),
                )
            );
        }
        return $this;
    }
}
