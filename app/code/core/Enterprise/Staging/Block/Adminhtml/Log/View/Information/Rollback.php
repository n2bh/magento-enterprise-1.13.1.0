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
 * Staging History Item View
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_View_Information_Rollback
    extends Enterprise_Staging_Block_Adminhtml_Log_View_Information_Default
{
    protected $_websites;

    /**
     * Retrieve target website on which backup was rollbacked
     * Returns array bc in map there is array of websites so there will no
     * problems in some cases in map will be several websites
     *
     * @return array
     */
    public function getTargetWebsites()
    {
        $_websites = array();
        foreach ($this->_mapper->getWebsites() as $stagingWebsiteId => $masterWebsite) {
            foreach ($masterWebsite as $id) {
                $_website = Mage::app()->getWebsite($id);
                if ($_website) {
                    $_websites[] = $_website;
                }
            }
        }
        return $_websites;
    }
}
