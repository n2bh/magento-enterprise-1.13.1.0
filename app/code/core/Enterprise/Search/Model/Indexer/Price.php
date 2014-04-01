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
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * Enterprise search model indexer price
 *
 * @deprecated after 1.9.0.0
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Indexer_Price
{
    /**
     * Update category'es products price index
     *
     * @return Enterprise_Search_Model_Indexer_Price
     */
    public function updatePriceIndexData()
    {
        $helper = Mage::helper('enterprise_search');
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            Mage::getResourceSingleton('enterprise_search/index')
                ->updatePriceIndexData();
        }

        return $this;
    }
}
