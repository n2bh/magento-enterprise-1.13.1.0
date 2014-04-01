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

abstract class Enterprise_GiftWrapping_Model_Sales_Pdf_Items_Abstract
    extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Prepare item lines
     *
     * @return array
     */
    abstract protected function _prepareLines();

    /**
     * Draw item line
     */
    public function draw()
    {
        $lineBlock = array(
            'lines'  => $this->_prepareLines(),
            'height' => 20
        );

        $page = $this->getPdf()->drawLineBlocks(
            $this->getPage(),
            array($lineBlock),
            array('table_header' => true)
        );

        $this->setPage($page);
    }
}
