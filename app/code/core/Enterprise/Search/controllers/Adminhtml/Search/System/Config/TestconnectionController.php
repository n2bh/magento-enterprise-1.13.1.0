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
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * Admin search test connection controller
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Adminhtml_Search_System_Config_TestconnectionController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for connection to server
     */
    public function pingAction()
    {
        if (!isset($_REQUEST['host']) || !($host = $_REQUEST['host'])
            || !isset($_REQUEST['port']) || !($port = (int)$_REQUEST['port'])
            || !isset($_REQUEST['path']) || !($path = $_REQUEST['path'])
        ) {
            echo 0;
            die;
        }

        $pingUrl = 'http://' . $host . ':' . $port . '/' . $path . '/admin/ping';

        if (isset($_REQUEST['timeout'])) {
            $timeout = (int)$_REQUEST['timeout'];
            if ($timeout < 0) {
                $timeout = -1;
            }
        } else {
            $timeout = 0;
        }

        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'HEAD',
                    'timeout' => $timeout
                )
            )
        );

        // attempt a HEAD request to the solr ping page
        $ping = @file_get_contents($pingUrl, false, $context);

        // result is false if there was a timeout
        // or if the HTTP status was not 200
        if ($ping !== false) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
