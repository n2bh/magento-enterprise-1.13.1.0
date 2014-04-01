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
 * One page checkout order review xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Onepage_Review extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Order review xml renderer
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $xmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObject = Mage::getModel('xmlconnect/simplexml_element', '<order></order>');
        $quote = $this->getQuote();

        $this->getChild('items')->addCartProductsToXmlObj($xmlObject, $quote);

        /**
         * Cart Totals
         */
        $this->getChild('totals')->setCartXmlObject($xmlObject)->toHtml();

        /**
         * Agreements
         */
        $agreements = $this->getChildHtml('agreements');
        if ($agreements) {
            $agreementsXmlObj = Mage::getModel('xmlconnect/simplexml_element', $agreements);
            $xmlObject->appendChild($agreementsXmlObj);
        }
        return $xmlObject->asNiceXml();
    }
}
