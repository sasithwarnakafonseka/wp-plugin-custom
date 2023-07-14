<?php
/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.1.2
 */

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

/**********************************
@@@@@ Custom Columns Reg Form
***********************************/

function pdandroidfcm_set_columns( $columns )
{
	$newColumns = array();
	$newColumns['cb'] = "Multiselect";
	$newColumns['user_email'] = 'Name';
	$newColumns['device_token'] = 'Token';
	//$newColumns['device'] = 'Devices';
	//$newColumns['os_version'] = 'OS Version';
	$newColumns['taxonomy-subscriptions'] = 'Subscribed To';
	$newColumns['added_on'] = 'Added On';
    //$newColumns['send_fcm'] = 'Send FCM';
	return $newColumns;
}

function pdandroidfcm_custom_columns( $column, $post_id )
{

	switch( $column ){

		case 'user_email' :
        echo get_the_author( $post_id );
		break;

		case 'device_token' :
		$device_token = pdandroidfcm_get_column_data( $post_id, 'device_token');
		echo "<span title='{$device_token}'>" . mb_strimwidth( $device_token, 0, 35, '...' ) . "</span>";
		break;

		case 'device' :
		$device = pdandroidfcm_get_column_data( $post_id, 'device');
		echo $device;
		break;

		case 'os_version' :
		$os_version = pdandroidfcm_get_column_data( $post_id, 'os_version');
		echo $os_version;
		break;

		case 'subscribed' :
		$subscribed = get_subscribed_term_names( $post_id, 'subscriptions' ) ;
		echo $subscribed;
		break;

		case 'added_on' :
		$added_on = get_the_date( get_option( 'date_format' ), $post_id );
		echo $added_on;
		break;
        
        //case 'send_fcm' :
		//$send_fcm = get_send_fcm($post_id);
		//echo $send_fcm;
		//break;
	}

}

function pdandroidfcm_make_registered_column_sortable( $columns ) {
	return wp_parse_args(array(
		'added_on' => 'orderby',
		'taxonomy-subscriptions' => 'orderby',
		'os_version' => 'orderby',
		'device' => 'orderby',
		'user_email' => 'orderby'
	), $columns );
}