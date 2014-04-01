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
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize banners manage page
     *
     * @return void
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'enterprise_banner';
        $this->_headerText = Mage::helper('enterprise_banner')->__('Manage Banners');
        $this->_addButtonLabel = Mage::helper('enterprise_banner')->__('Add Banner');
        parent::__construct();
    }
}
