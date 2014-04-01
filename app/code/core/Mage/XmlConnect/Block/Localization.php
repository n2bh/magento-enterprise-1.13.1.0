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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Localization list renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Localization extends Mage_Core_Block_Abstract
{
    /**
     * Render home category list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $l10nXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $l10nXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<localization></localization>');

        /** @var $translateHelper Mage_XmlConnect_Helper_Translate */
        $translateHelper = Mage::helper('xmlconnect/translate');

        foreach ($translateHelper->getLocalizationArray() as $key => $string) {
            $l10nXmlObj->addCustomChild($key, $string);
        }

        return $l10nXmlObj->asNiceXml();
    }
}
