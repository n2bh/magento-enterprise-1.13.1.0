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
 * Wishlist list container
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Model_Container_Wishlists extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_WISHLISTS_' . md5($this->_getIdentifier());
    }

    /**
     * Retrieve cache identifier
     *
     * @return string
     */
    public function getCacheId()
    {
        return $this->_getCacheId();
    }

    /**
     * Render block content from placeholder
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        return $block->toHtml();
    }
}
