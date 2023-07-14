<?php

/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.0
 */

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

/**********************************
@@@@@ ADDING CUSTOM DATABASE
 ***********************************/

global $wpdb;
$table_name = $wpdb->prefix . "pd_android_fcm";
$charset_collate = $wpdb->get_charset_collate();
$pd_android_fcm_db_version = get_option('pd_android_fcm_db_version', '1.0.0');

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name && current_user_can('administrator')) {
	$sql = "

	CREATE TABLE " . $table_name . " (
	`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`post_ID` INT(6) DEFAULT NULL,
	`device_token` varchar(255) DEFAULT NULL,
	`device` varchar(255) DEFAULT NULL,
	`os_version` varchar(50) DEFAULT NULL,
	`added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
	) $charset_collate;
	";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	if ($wpdb->last_error) {
		wp_die('Database error while installing pd Android FCM Plugin: <br><b style="color:red">"' . $wpdb->last_error . '"</b>', 'Creating Table Error!', ['back_link' => true]);
	}
	add_option('pd_android_fcm_db_version', '1.0.0');
}

/*if ( version_compare( $pd_android_fcm_db_version, '1.0.1' ) < 0 )
{
	global $wpdb;
	$sql = "

	CREATE TABLE `pd_android_fcm` (
	`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`device_token` varchar(255) DEFAULT NULL,
	`device` varchar(255) DEFAULT NULL,
	`os_version` varchar(50) DEFAULT NULL,
	`added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
	) $charset_collate;

	";


	$wpdb->query( "ALTER TABLE pd_android_fcm DROP COLUMN user_email" );
	dbDelta( $sql );

	update_option( 'pd_android_fcm_db_version', '1.0.1' );

}*/