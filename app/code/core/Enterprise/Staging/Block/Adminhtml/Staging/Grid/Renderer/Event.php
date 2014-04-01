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
 * @package     Enterprise_Staging
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Staging_Block_Adminhtml_Staging_Grid_Renderer_Event
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * Render a link to staging log entry
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $result = parent::render($row);
        return '<a href="' . $this->getUrl('*/staging_log/view',array('id'=>$row->getLogId())) . '">'
            . $this->escapeHtml($result) . '</a>';
    }

}
