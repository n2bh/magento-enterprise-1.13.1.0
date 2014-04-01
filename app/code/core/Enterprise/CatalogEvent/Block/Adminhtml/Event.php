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
 * @package     Enterprise_CatalogEvent
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Catalog Events Adminhtml Block
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */

class Enterprise_CatalogEvent_Block_Adminhtml_Event extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_event';
        $this->_blockGroup = 'enterprise_catalogevent';
        $this->_headerText = Mage::helper('enterprise_catalogevent')->__('Manage Catalog Events');
        $this->_addButtonLabel = Mage::helper('enterprise_catalogevent')->__('Add Catalog Event');
        parent::__construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-catalogevent';
    }
}
