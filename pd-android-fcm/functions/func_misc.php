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

@@@@@ MISCELLANEOUS FUNCTIONS 

***********************************/

add_theme_support( 'post-thumbnails' );
add_image_size( 'push_notification_image', '980', '444', true );

/*-------------- Removing add new link from left bar menu ---------------------*/

add_action( 'admin_menu', 'pdandroidfcm_adjust_the_wp_menu', 999 );
function pdandroidfcm_adjust_the_wp_menu() {
  //$page = remove_submenu_page( 'edit.php', 'post-new.php' );
  //or for custom post type 'myposttype'.
	remove_submenu_page( 'edit.php?post_type=pdandroidfcm', 'post-new.php?post_type=pdandroidfcm');
}

/*------------ Removing bulk actions from CPT----------------------*/

add_filter( 'bulk_actions-edit-pdandroidfcm', 'pdandroidfcm_p_remove_from_bulk_actions' );
function pdandroidfcm_p_remove_from_bulk_actions( $actions ){
	unset( $actions[ 'edit' ] );
	return $actions;
}

/*-------------------------------------------*/
if ( isset($_GET['post_type']) && $_GET['post_type'] == 'pdandroidfcm')
{
	add_action('admin_head', 'pdandroidfcm_custom_admin_css');
	function pdandroidfcm_custom_admin_css() {
		echo "<style>.tablenav .actions.bulkactions { padding-right: 0 } </style>";
	}
}
/*-------------------------------------*/
// Admin footer modification

$current_url = isset($_GET['post_type']) && !empty($_GET['post_type']) ? $_GET['post_type'] : '';
$pf_fcm_plugin_urls = array( 'pdandroidfcm', 'pdandroidfcm_msg','pdandroidfcm_user');

if ( in_array( $current_url, $pf_fcm_plugin_urls ) ) {
	add_filter('admin_footer_text', 'pdandroidfcm_remove_default_footer_admin_add_custom');
}
  
function pdandroidfcm_remove_default_footer_admin_add_custom () 
{
    echo '<span id="footer-thankyou">Developed by <a href="https://proficientdesigners.com" target="_blank">Proficient Designers</a></span><br>';
    echo '<span id="footer-thankyou">Visit our official site page to see <a href="https://proficientdesigners.in/creations/pd-android-fcm-push-notification/" target="_blank">documentation</a> of this plugin</span><br>';
}