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
 * @package     Enterprise_GiftCardAccount
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * GiftCard account history serource model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Resource_History extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary key field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftcardaccount/history', 'history_id');
    }

    /**
     * Setting "updated_at" date before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_GiftCardAccount_Model_Resource_History
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));

        return parent::_beforeSave($object);
    }
}
