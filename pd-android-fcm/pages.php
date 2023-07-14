<?php

/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.1
 * @param passing data to insert values in the custom table of pdandroidfcm
 */

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

/**
 * CUSTOM ADMIN PAGE FOR THIS PLUGIN
 */
function pdandroidfcm_admin_page_settings()
{
	$capability = current_user_can('manage_woocommerce') ? 'manage_woocommerce' : 'manage_options';
	add_submenu_page('edit.php?post_type=pdandroidfcm', 'Settings', 'Settings', $capability, 'settings', 'pdandroidfcm_page');
}

add_action('admin_menu', 'pdandroidfcm_admin_page_settings');

require_once 'settings.php';
