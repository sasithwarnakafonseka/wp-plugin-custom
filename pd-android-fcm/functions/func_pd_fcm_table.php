<?php
/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.0
 */

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

/**********************************
@@@@@ CUSTOM DB TABLE
***********************************/

/**
 * @param passing data to insert values in the custom table of pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_insert_data($val)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';

	$data = array(
		'post_ID'			=> $val['post_id'],
		'device_token' 		=> $val['pdandroidfcm_device_token'],
		'device' 			=> $val['pdandroidfcm_device'],
		'os_version' 		=> $val['pdandroidfcm_os_version']
	);

	$format = array(
		'%d',
		'%s', 
		'%s',
		'%s'
	);

	$wpdb->insert( $table, $data, $format );
	return $wpdb->insert_id;
}

/**
 * @param passing data to delete values in the custom table of pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_delete_data($id)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';

	$wpdb->delete( $table, array( 'post_ID' => $id ) );
}

/**
 * @param passing data to get post_ID from custom table of pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_get_device_post_id($device_token)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';
	
	$thepost = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE device_token = %s", $device_token ) );
	return $thepost->post_ID; 
}

/**
 * @param passing data to get values each column in the custom table of pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_get_column_data($id, $key)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';
	$result = $wpdb->get_row( "SELECT {$key} FROM {$table} WHERE post_ID = {$id}" );
	return $result->$key;
}

/**
 * @param passing token to see if token exists in pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_token_exists($token)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';
	$result = $wpdb->get_row( "SELECT * FROM {$table} WHERE device_token = '{$token}'" );
	if ($result)
		return true;
}

/**
 * @param passing user to see if user exists in pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_user_exists($userId,$newToken)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';
	$args = array(
		'author'        =>  $userId,
		'post_type'     => 'pdandroidfcm',
	);
	$current_user_posts = get_posts($args);
	$current_user_posts = reset($current_user_posts);
    
	if($current_user_posts){
    $dbData = array();
$dbData['device_token'] = $newToken;
$wpdb->update($table, $dbData, array('post_ID' => $current_user_posts->ID));
    	
        return true;
		
    }else{
    	return false;
    }
	
	
}

/**
 * @param passing token to see if token exists in pdandroidfcm
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_email_exists($token)
{
	global $wpdb;
	$table = $wpdb->prefix.'pd_android_fcm';
	$result = $wpdb->get_row( "SELECT * FROM {$table} WHERE device_token = '{$token}'" );
	if ($result)
		return true;
}

/**
 * @param deleting the entire device details for custom table when deleted from wp backend
 * @since 1.0.0
 * @version 1.0.0
 */
add_action( 'init', 'pdandroidfcm_init_custom_table_row_del' );
function pdandroidfcm_init_custom_table_row_del() {
    add_action( 'delete_post', 'pdandroidfcm_delete_data_from_backend', 10 );
}

function pdandroidfcm_delete_data_from_backend( $pid )
{
    global $wpdb;
    $table = $wpdb->prefix.'pd_android_fcm';
    
    if ( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE post_ID = %d", $pid ) ) ) {
        $wpdb->delete( $table, array( 'post_ID' => $pid ) );
    }
}

function get_send_fcm($post_id){
	return $post_id;
}