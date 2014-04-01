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
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Mage_CatalogIndex_Model_Catalog_Index_Flag extends Mage_Core_Model_Flag
{
    protected $_flagCode = 'catalogindex';

    const STATE_QUEUED = 1;
    const STATE_RUNNING = 2;


    protected function _beforeSave()
    {
        switch ($this->getState()) {
            case self::STATE_QUEUED:
                $this->setFlagData($this->getQueueInfo());
                break;

            case self::STATE_RUNNING:
                $this->setFlagData(getmypid());
                break;

            default:
                break;
        }

        return parent::_beforeSave();
    }
}
