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

$banners = array(
    array(
        'top.container',
        'Free Shipping on All Handbags',
        '<a href="{{store direct_url="apparel/women/handbags"}}"> '
            . '<img class="callout" title="Get Free Shipping on All Items under Handbags" '
            . 'src="{{skin url="images/callouts/home/free_shipping_all_handbags.jpg"}}" '
            . 'alt="Free Shipping on All Handbags" /></a>'
    ),
    array(
        'footer.before',
        '15% off Our New Evening Dresses',
        '<a href="{{store direct_url="apparel/women/evening-dresses"}}"> '
        . '<img class="callout" title="15% off Our New Evening Dresses" '
        . 'src="{{skin url="images/callouts/home/15_off_new_evening_dresses.jpg"}}" '
        . 'alt="15% off Our New Evening Dresses" /></a>'
    )
);

foreach ($banners as $sortOrder => $bannerData) {
    $banner = Mage::getModel('enterprise_banner/banner')
        ->setName($bannerData[1])
        ->setIsEnabled(1)
        ->setStoreContents(array(0 => $bannerData[2]))
        ->save();

    $widgetInstance = Mage::getModel('widget/widget_instance')
        ->setData('page_groups', array(
            array(
                'page_group' => 'pages',
                'pages'      => array(
                    'page_id'       => 0,
                    'for'           => 'all',
                    'layout_handle' => 'cms_index_index',
                    'block'         => $bannerData[0],
                    'template'      => 'banner/widget/block.phtml'
            ))
        ))
        ->setData('store_ids', '0')
        ->setData('widget_parameters', array(
            'display_mode' => 'fixed',
            'types'        => array(''),
            'rotate'       => '',
            'banner_ids'   => $banner->getId(),
            'unique_id'    => Mage::helper('core')->uniqHash()
        ))
        ->addData(array(
            'instance_type'          => 'enterprise_banner/widget_banner',
            'package_theme' => 'enterprise/default',
            'title'         => $bannerData[1],
            'sort_order'    => $sortOrder
        ))
        ->save();
}
