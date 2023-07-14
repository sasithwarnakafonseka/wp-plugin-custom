<?php

/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.1.2
 * @param passing data of api parameters
 * @description Api to Subscribe device
 */

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

/**********************************
@@@@@ REST API FOR SUBSCRIBING DEVICES OR USERS
***********************************/

add_action('rest_api_init', 'pdandroidfcm_unsubscribe_api_hooks');
// API custom endpoints for WP-REST API
function pdandroidfcm_unsubscribe_api_hooks()
{

    register_rest_route(
        'pd',
        '/fcm/unsubscribe/',
        array(
            'methods'  => 'GET',
            'callback' => 'pdandroidfcm_unsubscribe_api_callback',
            'permission_callback' => '__return_true'
        )
    );

    function pdandroidfcm_unsubscribe_api_callback($request)
    {
        $fields = [ 'api_secret_key', 'unsubscribe_with' ];

		foreach ($fields as $field) {
			if (!isset($request[$field])) {
				echo json_encode(['status' => 'error', 'msg' => 'required parameters missing, please read the documentation for correct parameters']);
				return;
			}
		}
        
        $pdandroidfcm_unsubcribe_with = sanitize_text_field($request['unsubscribe_with']);
        $pdandroidfcm_api_secret_key  = sanitize_text_field($request['api_secret_key']);
        $pdandroidfcm_device_token    = sanitize_text_field($request['device_token']);

        if( get_option( 'PD_FCM_API_SECRET_KEY' ) !== $pdandroidfcm_api_secret_key ) {

			$res = array('status' => 'warning', 'message' => 'incorrect api secret key passed' );
			return $res;
		}
        
        // unregister the device
        if ($pdandroidfcm_unsubcribe_with == 'token') {
            // checking for the parameter device_token
            if(!$pdandroidfcm_device_token) {
                $res = array('status' => 'error', 'message' => 'device token not passed, please read the documentation for correct parameters');
                return $res;
            }
            // checking for token exist
            else if(! pdandroidfcm_token_exists($pdandroidfcm_device_token)) {
                $res = array('status' => 'error', 'message' => 'device token does not exist');
                return $res;
            }
            // deleting the device 
            else {
                $postid = pdandroidfcm_get_device_post_id($pdandroidfcm_device_token);
                // deleting the post
                wp_delete_post($postid, false);
                // delting the fcm data
                pdandroidfcm_delete_data($postid);
                $res = array('status' => 'ok', 'message' => 'device token unregistered');
                return $res;
            }
        }        
        // unregister the user
        else if ($pdandroidfcm_unsubcribe_with == 'user_email') {

            $pdandroidfcm_user_email      = sanitize_text_field($request['user_email']);
            $posts = find_posts_by_email($pdandroidfcm_user_email, 'pdandroidfcm');
            if($posts){
                $deleted_ids = [];
                foreach ($posts as $post) {
                    // deleting the post
                    $deleted_ids[] = wp_delete_post($post->ID, false);
                    // delting the fcm data
                    pdandroidfcm_delete_data($post->ID);
                }
                $res = array('status' => 'ok', 'message' => 'user with multiple devices unregistered');
                return $res;
            }
            else {
                $res = array('status' => 'error', 'message' => 'user email or token does not exist');
                return $res;
            }
        }
        else {
            $res = array('status' => 'error', 'message' => 'user email or token does not exist');
            return $res;
        }
    }
}

if (!function_exists('find_posts_by_email')) {
    function find_posts_by_email($page_title, $post_type = false, $output = OBJECT)
    {
        global $wpdb;
        //Handle specific post type?
        $post_type_where = $post_type ? 'AND post_type = %s' : '';
        //Query all columns so as not to use get_post()
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = %s $post_type_where AND post_status = 'publish'", $page_title, $post_type ? $post_type : ''));

        if ($results) {
            $output = array();
            foreach ($results as $post) {
                $output[] = $post;
            }
            return $output;
        }
        return null;
    }
}
