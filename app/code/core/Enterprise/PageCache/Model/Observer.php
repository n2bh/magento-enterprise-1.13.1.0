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
 * Full page cache observer
 *
 * @category   Enterprise
 * @package    Enterprise_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Model_Observer
{
    /*
     * Design exception key
     */
    const XML_PATH_DESIGN_EXCEPTION = 'design/package/ua_regexp';

    /**
     * Page Cache Processor
     *
     * @var Enterprise_PageCache_Model_Processor
     */
    protected $_processor;

    /**
     * Page Cache Config
     *
     * @var Enterprise_PageCache_Model_Config
     */
    protected $_config;

    /**
     * Is Enabled Full Page Cache
     *
     * @var bool
     */
    protected $_isEnabled;

    /**
     * Class constructor
     */
    public function __construct(array $args = array())
    {
        $this->_processor = isset($args['processor'])
            ? $args['processor']
            : Mage::getSingleton('enterprise_pagecache/processor');
        $this->_config = isset($args['config']) ? $args['config'] : Mage::getSingleton('enterprise_pagecache/config');
        $this->_isEnabled = isset($args['enabled']) ? $args['enabled'] : Mage::app()->useCache('full_page');
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * Save page body to cache storage
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function cacheResponse(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $frontController = $observer->getEvent()->getFront();
        $request = $frontController->getRequest();
        $response = $frontController->getResponse();
        $this->_saveDesignException();
        $this->_processor->processRequestResponse($request, $response);
        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $action->getRequest();

        $noCache = $this->_getCookie()->get(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE);
        if ($noCache) {
            Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(false);
            $this->_getCookie()->renew(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE);
        } elseif ($action) {
            Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(true);
        }
        /**
         * Check if request will be cached
         */
        if ($this->_processor->canProcessRequest($request)) {
            Mage::app()->getCacheInstance()->banUse(Mage_Core_Block_Abstract::CACHE_GROUP);
        }
        $this->_getCookie()->updateCustomerCookies();
        return $this;
    }

    /**
     * Checks whether exists design exception value in cache.
     * If not, gets it from config and puts into cache
     *
     * @return Enterprise_PageCache_Model_Observer
     */
    protected function _saveDesignException()
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $cacheId = Enterprise_PageCache_Model_Processor::DESIGN_EXCEPTION_KEY;

        if (!Enterprise_PageCache_Model_Cache::getCacheInstance()->getFrontend()->test($cacheId)) {
            $exception = Mage::getStoreConfig(self::XML_PATH_DESIGN_EXCEPTION);
            Enterprise_PageCache_Model_Cache::getCacheInstance()
                ->save($exception, $cacheId, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
            $this->_processor->refreshRequestIds();
        }
        return $this;
    }

    /**
     * model_load_after event processor. Collect tags of all loaded entities
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerModelTag(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var $object Mage_Core_Model_Abstract */
        $object = $observer->getEvent()->getObject();
        if ($object && $object->getId()) {
            $tags = $object->getCacheIdTags();
            if ($tags) {
                $this->_processor->addRequestTag($tags);
            }
        }
        return $this;
    }

    /**
     * Retrieve block tags and add it to processor
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerBlockTags(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        /** @var $block Mage_Core_Block_Abstract*/
        $block = $observer->getEvent()->getBlock();
        if (in_array($block->getType(), array_keys($this->_config->getDeclaredPlaceholders()))) {
            return $this;
        }

        $tags = $block->getCacheTags();
        if (empty($tags)) {
            return $this;
        }
        $key = array_search(Mage_Core_Block_Abstract::CACHE_GROUP, $tags);
        if (false !== $key) {
            unset($tags[$key]);
        }
        if (empty($tags)) {
            return $this;
        }
        $this->_processor->addRequestTag($tags);

        return $this;
    }

    /**
     * Check category state on post dispatch to allow category page be cached
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function checkCategoryState(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $category = Mage::registry('current_category');
        /**
         * Categories with category event can't be cached
         */
        if ($category && $category->getEvent()) {
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            $request->setParam('no_cache', true);
        }
        return $this;
    }

    /**
     * Check product state on post dispatch to allow product page be cached
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function checkProductState(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $product = Mage::registry('current_product');
        /**
         * Categories with category event can't be cached
         */
        if ($product && $product->getEvent()) {
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            $request->setParam('no_cache', true);
        }
        return $this;
    }

    /**
     * Check if data changes duering object save affect cached pages
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function validateDataChanges(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        Mage::getModel('enterprise_pagecache/validator')->checkDataChange($object);
        return $this;
    }

    /**
     * Check if data delete affect cached pages
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function validateDataDelete(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        Mage::getModel('enterprise_pagecache/validator')->checkDataDelete($object);
        return $this;
    }

    /**
     * Process entity action
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function processEntityAction(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        Mage::getModel('enterprise_pagecache/validator')->cleanEntityCache($object);
        return $this;
    }

    /**
     * Clean full page cache
     *
     * @return Enterprise_PageCache_Model_Observer
     */
    public function cleanCache()
    {
        Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        return $this;
    }

    /**
     * Cleans cache by tags
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     */
    public function cleanCacheByTags(Varien_Event_Observer $observer)
    {
        /** @var $tags array */
        $tags = $observer->getEvent()->getTags();
        if (empty($tags)) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean();
            return $this;
        }

        Enterprise_PageCache_Model_Cache::getCacheInstance()->clean($tags);
        return $this;
    }

    /**
     * Clean expired entities in full page cache
     * @return Enterprise_PageCache_Model_Observer
     */
    public function cleanExpiredCache()
    {
        Enterprise_PageCache_Model_Cache::getCacheInstance()->getFrontend()->clean(Zend_Cache::CLEANING_MODE_OLD);
        return $this;
    }

    /**
     * Invalidate full page cache
     * @return Enterprise_PageCache_Model_Observer
     */
    public function invalidateCache()
    {
        Mage::app()->getCacheInstance()->invalidateType('full_page');
        return $this;
    }

    /**
     * Render placeholder tags around the block if needed
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function renderBlockPlaceholder(Varien_Event_Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        $placeholder = $this->_config->getBlockPlaceholder($block);

        if ($transport && $placeholder && !$block->getSkipRenderTag()) {
            $blockHtml = $transport->getHtml();

            $request = Mage::app()->getFrontController()->getRequest();
            /** @var $processor Enterprise_PageCache_Model_Processor_Default */
            $processor = $this->_processor->getRequestProcessor($request);
            if ($processor && $processor->allowCache($request)) {
                $container = $placeholder->getContainerClass();
                if ($container && !Mage::getIsDeveloperMode()) {
                    $container = new $container($placeholder);
                    $container->setProcessor(Mage::getSingleton('enterprise_pagecache/processor'));
                    $container->setPlaceholderBlock($block);
                    $container->saveCache($blockHtml);
                }
            }

            $blockHtml = $placeholder->getStartTag() . $blockHtml . $placeholder->getEndTag();
            $transport->setHtml($blockHtml);
        }
        return $this;
    }

    /**
     * Check cache settings for specific block type and associate block to container if needed
     *
     * @param Varien_Event_Observer $observer
     * @deprecated after 1.8
     * @return Enterprise_PageCache_Model_Observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $block  = $observer->getEvent()->getBlock();
        $placeholder = $this->_config->getBlockPlaceholder($block);
        if ($placeholder) {
            $block->setFrameTags($placeholder->getStartTag(), $placeholder->getEndTag());
        }
        return $this;
    }

    /**
     * Set cart hash in cookie on quote change
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerQuoteChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var Mage_Sales_Model_Quote */
        $quote = ($observer->getEvent()->getQuote()) ? $observer->getEvent()->getQuote() :
            $observer->getEvent()->getQuoteItem()->getQuote();
        $this->_getCookie()->setObscure(Enterprise_PageCache_Model_Cookie::COOKIE_CART, 'quote_' . $quote->getId());

        $cacheId = Enterprise_PageCache_Model_Container_Advanced_Quote::getCacheId();
        Enterprise_PageCache_Model_Cache::getCacheInstance()->remove($cacheId);

        return $this;
    }

    /**
     * Set compare list in cookie on list change. Also modify recently compared cookie.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerCompareListChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $listItems = Mage::helper('catalog/product_compare')->getItemCollection();
        $previouseList = $this->_getCookie()->get(Enterprise_PageCache_Model_Cookie::COOKIE_COMPARE_LIST);
        $previouseList = (empty($previouseList)) ? array() : explode(',', $previouseList);

        $ids = array();
        foreach ($listItems as $item) {
            $ids[] = $item->getId();
        }
        sort($ids);
        $this->_getCookie()->set(Enterprise_PageCache_Model_Cookie::COOKIE_COMPARE_LIST, implode(',', $ids));

        //Recenlty compared products processing
        $recentlyComparedProducts = $this->_getCookie()
            ->get(Enterprise_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED);
        $recentlyComparedProducts = (empty($recentlyComparedProducts)) ? array()
            : explode(',', $recentlyComparedProducts);

        //Adding products deleted from compare list to "recently compared products"
        $deletedProducts = array_diff($previouseList, $ids);
        $recentlyComparedProducts = array_merge($recentlyComparedProducts, $deletedProducts);

        //Removing products from recently product list if it's present in compare list
        $addedProducts = array_diff($ids, $previouseList);
        $recentlyComparedProducts = array_diff($recentlyComparedProducts, $addedProducts);

        $recentlyComparedProducts = array_unique($recentlyComparedProducts);
        sort($recentlyComparedProducts);

        $this->_getCookie()->set(Enterprise_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED,
            implode(',', $recentlyComparedProducts));

       return $this;
    }

    /**
     * Set new message cookie on adding messsage to session.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function processNewMessage(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getCookie()->set(Enterprise_PageCache_Model_Cookie::COOKIE_MESSAGE, '1');
        return $this;
    }


    /**
     * Update customer viewed products index and renew customer viewed product ids cookie
     *
     * @return Enterprise_PageCache_Model_Observer
     */
    public function updateCustomerProductIndex()
    {
        try {
            $productIds = $this->_getCookie()->get(Enterprise_PageCache_Model_Container_Viewedproducts::COOKIE_NAME);
            if ($productIds) {
                $productIds = explode(',', $productIds);
                Mage::getModel('reports/product_index_viewed')->registerIds($productIds);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        // renew customer viewed product ids cookie
        $countLimit = Mage::getStoreConfig(Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        $collection = Mage::getResourceModel('reports/product_index_viewed_collection')
            ->addIndexFilter()
            ->setAddedAtOrder()
            ->setPageSize($countLimit)
            ->setCurPage(1);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);
        $productIds = $collection->load()->getLoadedIds();
        $productIds = implode(',', $productIds);
        $this->_getCookie()->registerViewedProducts($productIds, $countLimit, false);
        return $this;
    }

    /**
     * Set cookie for logged in customer
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getCookie()->updateCustomerCookies();
        $this->updateCustomerProductIndex();
        return $this;
    }

    /**
     * Remove customer cookie
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getCookie()->updateCustomerCookies();

        if (!$this->_getCookie()->get(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER)) {
            $this->_getCookie()->delete(Enterprise_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED);
            $this->_getCookie()->delete(Enterprise_PageCache_Model_Cookie::COOKIE_COMPARE_LIST);
            Enterprise_PageCache_Model_Cookie::registerViewedProducts(array(), 0, false);
        }

        return $this;
    }

    /**
     * Set wishlist hash in cookie on wishlist change
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerWishlistChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = '';
        foreach (Mage::helper('wishlist')->getWishlistItemCollection() as $item) {
            $cookieValue .= ($cookieValue ? '_' : '') . $item->getId();
        }

        // Wishlist sidebar hash
        $this->_getCookie()->setObscure(Enterprise_PageCache_Model_Cookie::COOKIE_WISHLIST, $cookieValue);

        // Wishlist items count hash for top link
        $this->_getCookie()->setObscure(Enterprise_PageCache_Model_Cookie::COOKIE_WISHLIST_ITEMS,
            'wishlist_item_count_' . Mage::helper('wishlist')->getItemCount());

        Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(
            Mage::helper('wishlist')->getWishlist()->getCacheIdTags()
        );

        return $this;
    }

    /**
     * Register add wishlist item from cart in admin
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerAdminWishlistChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(
            $observer->getEvent()->getWishlist()->getCacheIdTags()
        );
    }

    /**
     * Clear wishlist list
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerWishlistListChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $blockContainer = Mage::getModel('enterprise_pagecache/container_wishlists');
        Enterprise_PageCache_Model_Cache::getCacheInstance()->remove($blockContainer->getCacheId());

        return $this;
    }

    /**
     * Set poll hash in cookie on poll vote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerPollChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = $observer->getEvent()->getPoll()->getId();
        $this->_getCookie()->set(Enterprise_PageCache_Model_Cookie::COOKIE_POLL, $cookieValue);

        return $this;
    }

    /**
     * Clean order sidebar cache
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerNewOrder(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        /** @var $blockContainer Enterprise_PageCache_Model_Container_Orders */
        $blockContainer = Mage::getModel('enterprise_pagecache/container_orders');
        Enterprise_PageCache_Model_Cache::getCacheInstance()->remove($blockContainer->getCacheId());
        return $this;
    }

    /**
     * Remove new message cookie on clearing session messages.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function processMessageClearing(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getCookie()->delete(Enterprise_PageCache_Model_Cookie::COOKIE_MESSAGE);
        return $this;
    }

    /**
     * Resave exception rules to cache storage
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerDesignExceptionsChange(Varien_Event_Observer $observer)
    {
        $object = $observer->getDataObject();
        Enterprise_PageCache_Model_Cache::getCacheInstance()
            ->save($object->getValue(), Enterprise_PageCache_Model_Processor::DESIGN_EXCEPTION_KEY,
                array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
        return $this;
    }

    /**
     * Retrieve cookie instance
     *
     * @return Enterprise_PageCache_Model_Cookie
     */
    protected function _getCookie()
    {
        return Mage::getSingleton('enterprise_pagecache/cookie');
    }

    /**
     * Check if last viewed product id should be processed after cached product view
     * @deprecated after 1.8 - added dynamic block generation
     */
    protected function _checkViewedProducts()
    {
        $varName = Enterprise_PageCache_Model_Processor::LAST_PRODUCT_COOKIE;
        $productId = (int) Mage::getSingleton('core/cookie')->get($varName);
        if ($productId) {
            $model = Mage::getModel('reports/product_index_viewed');
            if (!$model->getCount()) {
                $product = Mage::getModel('catalog/product')->load($productId);
                if ($product->getId()) {
                    $model->setProductId($productId)
                        ->save()
                        ->calculate();
                }
            }
            Mage::getSingleton('core/cookie')->delete($varName);
        }
    }

    /**
     * Update info about product on product page
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function updateProductInfo(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $paramsObject = $observer->getEvent()->getParams();
        if ($paramsObject instanceof Varien_Object) {
            if (array_key_exists(Enterprise_PageCache_Model_Cookie::COOKIE_CATEGORY_ID, $_COOKIE)) {
                $paramsObject->setCategoryId($_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CATEGORY_ID]);
            }
        }
        return $this;
    }

    /**
     * Check cross-domain session messages
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function checkMessages(Varien_Event_Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        if (!$transport || !$transport->getUrl()) {
            return $this;
        }
        $url = $transport->getUrl();
        $httpHost = Mage::app()->getFrontController()->getRequest()->getHttpHost();
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($httpHost != $urlHost && Mage::getSingleton('core/session')->getMessages()->count() > 0) {
            $transport->setUrl(Mage::helper('core/url')->addRequestParam(
                $url,
                array(Enterprise_PageCache_Model_Cache::REQUEST_MESSAGE_GET_PARAM => null)
            ));
        }
        return $this;
    }

    /**
     * Observer on changed Customer SegmentIds
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function changedCustomerSegmentIds(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return;
        }
        $segmentIds = is_array($observer->getSegmentIds()) ? $observer->getSegmentIds() : array();
        $segmentsIdsString= implode(',', $segmentIds);
        $this->_getCookie()->set(Enterprise_PageCache_Model_Cookie::CUSTOMER_SEGMENT_IDS, $segmentsIdsString);
    }

    /**
     * Drop top navigation block from cache if category becomes visible/invisible
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerCategorySave(Varien_Event_Observer $observer)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $observer->getEvent()->getDataObject();

        if ($category->isObjectNew() ||
            ($category->dataHasChangedFor('is_active') || $category->dataHasChangedFor('include_in_menu'))
        ) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(Mage_Catalog_Model_Category::CACHE_TAG);
        }
    }

    /**
     * Register form key in session from cookie value
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerCachedFormKey(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return;
        }

        /** @var $session Mage_Core_Model_Session  */
        $session = Mage::getSingleton('core/session');
        $cachedFrontFormKey = Enterprise_PageCache_Model_Cookie::getFormKeyCookieValue();
        if ($cachedFrontFormKey) {
            $session->setData('_form_key', $cachedFrontFormKey);
        }
    }

    /**
     * Clean cached tags for product if tags for product are saved
     *
     * @param Varien_Event_Observer $observer
     */
    public function cleanCachedProductTagsForTags(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return;
        }

        /** @var $tagModel Mage_Tag_Model_Tag */
        $tagModel = $observer->getEvent()->getDataObject();
        $productCollection = $tagModel->getEntityCollection()
            ->addTagFilter($tagModel->getId());

        /** @var $product Mage_Catalog_Model_Product */
        foreach ($productCollection as $product) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean($product->getCacheTags());
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
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $redirect = $observer->getEvent()->getRedirect();
        Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(
            array(
                Enterprise_PageCache_Helper_Url::prepareRequestPathTag($redirect->getData('identifier')),
                Enterprise_PageCache_Helper_Url::prepareRequestPathTag($redirect->getData('target_path')),
                Enterprise_PageCache_Helper_Url::prepareRequestPathTag($redirect->getOrigData('identifier')),
                Enterprise_PageCache_Helper_Url::prepareRequestPathTag($redirect->getOrigData('target_path'))
            )
        );
        return $this;
    }
}
