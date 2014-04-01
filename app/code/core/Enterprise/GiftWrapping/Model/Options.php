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
 * @package     Enterprise_GiftWrapping
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift wrapping options model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Model_Options extends Varien_Object
{
    /**
     * Current data object
     */
    protected $_dataObject = null;

    /**
     * Set gift wrapping options data object
     *
     * @param Varien_Object $item
     * @return Enterprise_GiftWrapping_Model_Options
     */
    public function setDataObject($item)
    {
        if ($item instanceof Varien_Object && $item->getGiftwrappingOptions()) {
            $this->addData(unserialize($item->getGiftwrappingOptions()));
            $this->_dataObject = $item;
        }
        return $this;
    }

   /**
     * Update gift wrapping options data object
     *
     * @return Enterprise_GiftWrapping_Model_Options
     */
    public function update()
    {
        $this->_dataObject->setGiftwrappingOptions(serialize($this->getData()));
        return $this;
    }
}
