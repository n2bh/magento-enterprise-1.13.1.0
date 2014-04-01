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
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * TargetRule Products Item Block
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 *
 * @method Enterprise_TargetRule_Block_Catalog_Product_Item setItem(Mage_Catalog_Model_Product $item)
 * @method Mage_Catalog_Model_Product getItem()
 */
class Enterprise_TargetRule_Block_Catalog_Product_Item extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Get cache key informative items with the position number to differentiate
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $elements = Mage::app()->getLayout()->getXpath('//action[@method="addPriceBlockType"]');
        if (is_array($elements)) {
            foreach ($elements as $element) {
                if (!empty($element->type)) {
                    $prefix = 'price_block_type_' . (string)$element->type;
                    $cacheKeyInfo[$prefix . '_block'] = empty($element->block) ? '' : (string)$element->block;
                    $cacheKeyInfo[$prefix . '_template'] = empty($element->template) ? '' : (string)$element->template;
                }
            }
        }
        $cacheKeyInfo[] = $this->getPosition();

        return $cacheKeyInfo;
    }
}
