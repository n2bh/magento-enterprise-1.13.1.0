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
 * Placeholder container for catalog product items
 */
class Enterprise_PageCache_Model_Container_CatalogProductItem
    extends Enterprise_PageCache_Model_Container_Advanced_Quote
{
    const BLOCK_NAME_RELATED           = 'CATALOG_PRODUCT_ITEM_RELATED';
    const BLOCK_NAME_UPSELL            = 'CATALOG_PRODUCT_ITEM_UPSELL';

    /**
     * Parent (container) block
     *
     * @var null|Enterprise_TargetRule_Block_Catalog_Product_List_Abstract
     */
    protected $_parentBlock;

    /**
     * Current item id
     *
     * @var null|int
     */
    protected $_itemId;

    /**
     * Container position in list
     *
     * @var null|int
     */
    protected $_itemPosition;

    /**
     * Info cache additional id
     *
     * @var null|string
     */
    protected $_infoCacheId = null;

    /**
     * Data shared between all instances of current container
     *
     * @var null|array
     */
    protected static $_sharedInfoData = array(
        self::BLOCK_NAME_RELATED => array(
            'first'  => true,
            'info'   => null,
        ),
        self::BLOCK_NAME_UPSELL => array(
            'first'  => true,
            'info'   => null,
        ),
    );

    /**
     * Get parent block type
     *
     * @return null|string
     */
    protected function _getListBlockType()
    {
        $blockName = $this->_placeholder->getName();
        if ($blockName == self::BLOCK_NAME_RELATED) {
            return 'enterprise_targetrule/catalog_product_list_related';
        } elseif ($blockName == self::BLOCK_NAME_UPSELL) {
            return 'enterprise_targetrule/catalog_product_list_upsell';
        }

        return null;
    }

    /**
     * Returns cache identifier for informational data about product lists
     *
     * @return string
     */
    protected function _getInfoCacheId()
    {
        if (is_null($this->_infoCacheId)) {
            $this->_infoCacheId = 'CATALOG_PRODUCT_LIST_SHARED_'
                . md5($this->_placeholder->getName()
                    . $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CART, '')
                    . $this->_getProductId());
        }
        return $this->_infoCacheId;
    }

    /**
     * Saves informational cache, containing parameters used to show lists.
     *
     * @return Enterprise_PageCache_Model_Container_CatalogProductItem
     */
    protected function _saveInfoCache()
    {
        $placeholderName = $this->_placeholder->getName();
        if (is_null(self::$_sharedInfoData[$placeholderName]['info'])) {
            return $this;
        }

        $data = array();
        $cacheRecord = Enterprise_PageCache_Model_Container_Abstract::_loadCache($this->_getCacheId());
        if ($cacheRecord) {
            $cacheRecord = json_decode($cacheRecord, true);
            if ($cacheRecord) {
                $data = $cacheRecord;
            }
        }
        $data[$this->_getInfoCacheId()] = self::$_sharedInfoData[$placeholderName]['info'];
        $data = json_encode($data);

        $tags = array(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        $lifetime = $this->_placeholder->getAttribute('cache_lifetime');
        if (!$lifetime) {
            $lifetime = false;
        }
        Enterprise_PageCache_Model_Cache::getCacheInstance()->save($data, $this->_getCacheId(), $tags, $lifetime);
        return $this;
    }

    /**
     * Get shared info param
     *
     * @param string|null $key
     * @return mixed
     */
    protected function _getSharedParam($key = null)
    {
        $placeholderName = $this->_placeholder->getName();
        $info = self::$_sharedInfoData[$placeholderName]['info'];
        if (is_null($info)) {
            $info = array();
            $cacheRecord = Enterprise_PageCache_Model_Cache::getCacheInstance()->load($this->_getCacheId());
            if ($cacheRecord) {
                $cacheRecord = json_decode($cacheRecord, true);
                if ($cacheRecord && array_key_exists($this->_getInfoCacheId(), $cacheRecord)) {
                    $info = $cacheRecord[$this->_getInfoCacheId()];
                }
            }
            self::$_sharedInfoData[$placeholderName]['info'] = $info;
        }
        return isset($key) ? (isset($info[$key]) ? $info[$key] : null) : $info;
    }

    /**
     * Set shared info param
     *
     * @param string $key
     * @param mixed $value
     * @return Enterprise_PageCache_Model_Container_CatalogProductItem
     */
    protected function _setSharedParam($key, $value)
    {
        $placeholderName = $this->_placeholder->getName();
        if (is_null(self::$_sharedInfoData[$placeholderName]['info'])) {
            $this->_getSharedParam();
        }
        self::$_sharedInfoData[$placeholderName]['info'][$key] = $value;

        return $this;
    }

    /**
     * Get parent (container) block
     *
     * @return false|Enterprise_TargetRule_Block_Catalog_Product_List_Abstract
     */
    protected function _getParentBlock()
    {
        if (is_null($this->_parentBlock)) {
            $blockType = $this->_getListBlockType();
            $this->_parentBlock = $blockType ? Mage::app()->getLayout()->createBlock($blockType) : false;
        }

        return $this->_parentBlock;
    }

    /**
     * Get next item id
     *
     * @return int|null
     */
    protected function _getItemId()
    {
        if (is_null($this->_itemId)) {
            // get all ids
            $ids = $this->_getSharedParam('ids');
            if (!$ids && !is_array($ids)) {
                $parentBlock = $this->_getParentBlock();
                if ($parentBlock) {
                    $productId = $this->_getProductId();
                    if ($productId && !Mage::registry('product')) {
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId(Mage::app()->getStore()->getId())
                            ->load($productId);
                        if ($product) {
                            Mage::register('product', $product);
                        }
                    }
                    $ids = Mage::registry('product') ? $parentBlock->getAllIds() : array();
                    $this->_setSharedParam('shuffled', $parentBlock->isShuffled());
                }
                if (!$ids) {
                    $ids = array();
                }
                $this->_setSharedParam('ids', $ids);
            }

            // preparations for first container
            $placeholderName = $this->_placeholder->getName();
            if (self::$_sharedInfoData[$placeholderName]['first']) {
                self::$_sharedInfoData[$placeholderName]['first'] = false;
                if (!isset(self::$_sharedInfoData[$placeholderName]['cursor'])) {
                    self::$_sharedInfoData[$placeholderName]['cursor'] = 0;
                }
                // check for shuffled
                if ($this->_getSharedParam('shuffled') && !empty($ids)) {
                    shuffle($ids);
                    $this->_setSharedParam('ids', $ids);
                }
            }

            if (is_null($this->_itemPosition)) {
                $this->_itemPosition = self::$_sharedInfoData[$placeholderName]['cursor'];
            }

            $this->_itemId = isset($ids[$this->_itemPosition]) ? $ids[$this->_itemPosition] : false;
        }

        return $this->_itemId;
    }

    /**
     * Pop current item id
     *
     * @return int
     */
    protected function _popItem()
    {
        if (!isset(self::$_sharedInfoData[$this->_placeholder->getName()]['cursor'])) {
            self::$_sharedInfoData[$this->_placeholder->getName()]['cursor'] = 0;
        }
        if (is_null($this->_itemPosition)) {
            $this->_itemPosition = self::$_sharedInfoData[$this->_placeholder->getName()]['cursor'];
        }
        return self::$_sharedInfoData[$this->_placeholder->getName()]['cursor']++;
    }

    /**
     * Generate and apply container content in controller after application is initialized
     *
     * @param string $content
     * @return bool
     */
    public function applyInApp(&$content)
    {
        $result = parent::applyInApp($content);
        $this->_saveInfoCache();
        return $result;
    }

    /**
     * Check if could be applied without application
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        // check if item ids were not generated before
        $ids = $this->_getSharedParam('ids');
        if (is_null($ids)) {
            $this->_popItem();
            return false;
        }

        $result = parent::applyWithoutApp($content);
        $this->_popItem();

        return $result;
    }

    /**
     * Render element that was not cached
     *
     * @return false|string
     */
    protected function _renderBlock()
    {
        $itemId = $this->_getItemId();
        if (!$itemId) {
            return '';
        }

        /** @var $item Mage_Catalog_Model_Product */
        $item = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($itemId);

        $block = $this->_getPlaceHolderBlock();
        $block->setItem($item);

        $priceBlock = $this->_placeholder->getAttribute('price_block_type_' . $item->getTypeId() . '_block');
        if (!empty($priceBlock)) {
            $block->addPriceBlockType(
                $item->getTypeId(),
                $priceBlock,
                $this->_placeholder->getAttribute('price_block_type_' . $item->getTypeId() . '_template')
            );
        }

        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }

    /**
     * Retrieve cache id
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return parent::_getCacheId() . $this->_getProductId();
    }
}
