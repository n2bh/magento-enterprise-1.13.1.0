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
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
/**
 * PageCache Data helper
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Character sets
     */
    const CHARS_LOWERS                          = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_UPPERS                          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHARS_DIGITS                          = '0123456789';

    /**
     * Get random generated string
     *
     * @param int $len
     * @param string|null $chars
     * @return string
     */
    public static function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }

    /**
     * Wrap string with placeholder wrapper
     *
     * @param string $string
     * @return string
     */
    public static function wrapPlaceholderString($string)
    {
        return '{{' . chr(1) . chr(2) . chr(3) . $string . chr(3) . chr(2) . chr(1) . '}}';
    }

    /**
     * Prepare content for saving
     *
     * @param string $content
     */
    public static function prepareContentPlaceholders(&$content)
    {
        /**
         * Replace all occurrences of session_id with unique marker
         */
        Enterprise_PageCache_Helper_Url::replaceSid($content);
        /**
         * Replace all occurrences of form_key with unique marker
         */
        Enterprise_PageCache_Helper_Form_Key::replaceFormKey($content);
    }
}
