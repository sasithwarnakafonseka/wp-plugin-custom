<?php

/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.0
 * @param passing data of api parameters
 * @description Api to Subscribe device
 */

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

/**********************************
@@@@@ REST API FOR SUBSCRIBING DEVICES OR USERS
 ***********************************/

add_action('rest_api_init', 'pdandroidfcm_subscribe_api_hooks');
// API custom endpoints for WP-REST API
function pdandroidfcm_subscribe_api_hooks()
{

	register_rest_route(
		'pd',
		'/fcm/subscribe/',
		array(
			'methods'  => 'POST',
			'callback' => 'pdandroidfcm_subscribe_api_callback',
			'permission_callback' => '__return_true'
		)
	);

	function pdandroidfcm_subscribe_api_callback($request)
	{

		$fields = ['api_secret_key','user_id', 'device_token'];

		foreach ($fields as $field) {
			if (!isset($request[$field])) {
				echo json_encode(['status' => 'error', 'msg' => 'required parameters missing, please read the documentation!']);
				return;
			}
		}

		$pdandroidfcm_api_secret_key  = sanitize_text_field($request['api_secret_key']);
		$pdandroidfcm_user_email      = sanitize_text_field($request['user_email']);
		$pdandroidfcm_device_token    = sanitize_text_field($request['device_token']);
		$pdandroidfcm_subscribed      = sanitize_text_field($request['subscribed']);
		$pdandroidfcm_device          = sanitize_text_field($request['device_name']);
		$pdandroidfcm_os_version      = sanitize_text_field($request['os_version']);
 		$pdandroidfcm_user            = sanitize_text_field($request['user_id']);

		$user =	get_userdata(str_replace("ISA","",$pdandroidfcm_user));
		$postarr = array(
			'post_title'    => $user->data->display_name,
			'post_author'   => $user->ID,
			'post_status'   => 'publish',
			'post_type'     => 'pdandroidfcm',
		);

		if (get_option('PD_FCM_API_SECRET_KEY') !== $pdandroidfcm_api_secret_key) {

			$res = array('status' => 'warning', 'message' => 'incorrect api secret key passed');
			return $res;
		}else if(pdandroidfcm_user_exists($pdandroidfcm_user,$pdandroidfcm_device_token)){
        $res = array('status' => 'warning', 'message' => 'device token updated');
			return $res;
        }else{
        $post_id = wp_insert_post($postarr, false);

			wp_set_object_terms($post_id, $pdandroidfcm_subscribed, 'subscriptions');

			pdandroidfcm_insert_data(
				array(
					'post_id'						=> $post_id,
					'pdandroidfcm_device_token'		=> $pdandroidfcm_device_token,
					'pdandroidfcm_device'			=> $pdandroidfcm_device,
					'pdandroidfcm_os_version'		=> $pdandroidfcm_os_version
				)
			);

			$res = array('status' => 'ok', 'message' => 'device token registered', 'registered_id' => $post_id);
			return $res;
        }
	}
}