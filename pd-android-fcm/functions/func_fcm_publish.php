<?php

/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.0
 * @description If a post is published trigger this push notification function
 */

defined('ABSPATH') or die('Sorry, you can\'t do what you want to do !!.');

/**********************************
@@@@@ SEND NOTIFICATION
***********************************/

if (!function_exists('wp_get_current_user')) {
	include(ABSPATH . "wp-includes/pluggable.php");
}

/*Sending push notification if a blog is posted*/
$PD_BLOG_AS_NOTIF = esc_attr(get_option('PD_BLOG_AS_NOTIF'));
if ($PD_BLOG_AS_NOTIF == 1) {
	if (current_user_can('administrator')) {
		add_action('publish_post', 'pdandroidfcm_send_published_notification', 10, 2);
	}
}

/*Sending push notification from Custom Push Notification*/
if (current_user_can('administrator')) {
	add_action('publish_pdandroidfcm_msg', 'pdandroidfcm_send_published_notification', 10, 2);
}

/*Sending push notification from chosen CPT*/
if (current_user_can('administrator')) {
	add_action('admin_init', 'pdandroidfcm_send_pn_cpt', 10, 1);

	function pdandroidfcm_send_pn_cpt()
	{
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		/*names or objects, note names is the default*/
		$output = 'objects';
		/*'and' or 'or'*/
		$operator = 'and';

		$post_types = get_post_types($args, $output, $operator);

		if ($post_types) {
			foreach ($post_types  as $post_type) {
				$options = get_option('PD_CPT_AS_NOTIF') ? get_option('PD_CPT_AS_NOTIF') : array('null');
				$pre_registered_cpt = $post_type->rewrite['slug'];

				if (in_array($pre_registered_cpt, $options)) {
					add_action('publish_' . $pre_registered_cpt, 'pdandroidfcm_send_published_notification', 10, 2);
				}
			}
		}
	}
}

/**
 * @param passing post ID and post
 * @description - to send push notifications to the particular post or custom push notification
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_send_published_notification($ID = null, $post = null)
{
	$title = $post->post_title;
	$message = get_the_excerpt($post);
	$image = get_the_post_thumbnail_url($post, 'push_notification_image');
	$post_id = $post->ID;

	$push = new PDANDROIDFCMPushMsg($title, $message, $image, $post_id);
	/*getting the push from push object*/
	$mPushNotification = $push->getPush();

	/*creating firebase class object */
	$firebase = new PDANDROIDFCMFirebase();

	/*Getting subscribed terms*/
	$term_names = pdandroidfcm_get_subscribed_term_names($post->ID, 'subscriptions');

	if (current_user_can('administrator')) {
		add_action('registered_taxonomy', 'pdandroidfcm_p_send_push_to_subscribers', 12, 3);
	}
	// send notification if the pd android fcm metabox option is checked for each blog post or custom post type
	if (isset($_POST['pd_send_push_notif_chk']) && $_POST['pd_send_push_notif_chk'] == 'on') {
		pdandroidfcm_p_send_push_to_subscribers($term_names, $firebase, $mPushNotification);
	}
	// send notification if the pdandroidfcm_msg cpt page send this notification 
	require_once(ABSPATH . 'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	if ($currentScreen->id === "pdandroidfcm_msg") {
		pdandroidfcm_p_send_push_to_subscribers($term_names, $firebase, $mPushNotification);
	}
}

/**
 * @param passing post ID
 * @description - getting subscription names of a post or custom notifications
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_get_subscribed_term_names($post_id)
{
	$args = array(
		'object_ids' => $post_id,
		'hide_empty' => true,
	);

	$terms = new WP_Term_Query($args);

	foreach ($terms->get_terms() as $term) {
		$res[] = $term->name;
	}
	return $res;
}

/**
 * @param passing term names
 * @description - send push notification to subscribers
 * @since 1.0.0
 * @version 1.0.0
 */
function pdandroidfcm_p_send_push_to_subscribers($term_names = null, $firebase = null, $mPushNotification = null)
{
	if ($term_names) {
		$args = array(
			'post_type' => 'pdandroidfcm',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'subscriptions',
					'field' => 'name',
					'terms' => $term_names,
				),
			),
		);
	} else {
		$args = array(
			'post_type' => 'pdandroidfcm',
			'posts_per_page' => -1,
			'post_status'    => 'publish'
		);
	}

	$terms = get_posts($args);
	foreach ($terms as $key) {
		$firebase->send(pdandroidfcm_get_column_data($key->ID, 'device_token'), $mPushNotification);
	}
}
