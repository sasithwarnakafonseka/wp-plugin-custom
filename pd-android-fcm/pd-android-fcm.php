<?php
/*
* @package pd-android-fcm
* Plugin Name: pd Android FCM Push Notification
* Plugin URI: https://proficientdesigners.in/creations/pd-android-fcm-push-notification/
* Description: pd Android FCM Push Notification is a plugin through which you can send push notifications directly from your WordPress site to android devices via <a href='https://firebase.google.com/' target='_blank'>Firebase Cloud Messaging</a> service. When a new blog is posted or existing blog is updated, a push notification sent to android device.
* Version: 1.1.8
* WC requires at least: 3.4.2
* WC tested up to: 4.6.1
* Author: Proficient Designers
* Author URI: https://proficientdesigners.com/
* Licence: GPLv2 or Later
* Text Domain: pd-android-fcm
*/

/**
 * Version checks
 */
global $wp_version;

if (version_compare($wp_version, '4.0', '<')) {
	wp_die(
		'Sorry, <b>pd Android FCM Push Notification</b> plugin requires WordPress 4.0 or newer. <p><a class="button" href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a></p>',
		'Warning !!',
		['back_link' => true]
	);
}

if (version_compare(phpversion(), '5.6', '<')) {
	wp_die(
		'Sorry, <b>pd Android FCM Push Notification</b> plugin requires php version 5.6 or above',
		'Warning !!',
		['back_link' => true]
	);
}
/**
 * * ABSPATH check
 */
defined('ABSPATH') || wp_die('Sorry, you can\'t do what you want to do !!.', 'Warning !!', ['back_link' => true]);

/**
 * pd Android FCM Plugin Class
 */
if (!class_exists('PDANDROIDFCM')) {
	class PDANDROIDFCM
	{
		public function __construct()
		{
			add_action('init', [$this, 'pdandroidfcm_cpt']);
			add_action('init', [$this, 'pdandroidfcm_send_push_notification_cpt']);
        	add_action('init', [$this, 'pdandroidfcm_send_push_notification_cpt_individual']);
			add_action('init', [$this, 'create_subscriptions_hierarchical_taxonomy'], 0);
			/*Registering Custom Columns*/
			add_filter('manage_pdandroidfcm_posts_columns', 'pdandroidfcm_set_columns');
			add_action('manage_pdandroidfcm_posts_custom_column', 'pdandroidfcm_custom_columns', 10, 2);
			add_action('manage_edit-pdandroidfcm_sortable_columns', 'pdandroidfcm_make_registered_column_sortable', 10, 1);

			/*Registering Custom Metaboxes*/
			add_action('add_meta_boxes', 'pdandroidfcm_add_meta_box', 10, 1);
			/*add_action( 'save_post', 'pdandroidfcm_device_data' );*/
			add_filter('plugin_action_links', [$this, 'pdandroidfcm_plugin_add_settings_link'], 10, 5);
			add_filter('bulk_actions-edit-post', [$this, 'bulk_action']);
			//check for this
			add_filter('post_row_actions', [$this, 'remove_row_actions'], 10, 2);
			// wp footer
			add_action('admin_footer', [$this, 'pd_fcm_js_scripts']);
		}

		public function activate()
		{
			/*register custom database*/
			$this->create_pdandroidfcm_table();
			/*flush rewrite rules to avoid rare conflicts*/
			flush_rewrite_rules();
		}
		public function remove_row_actions($actions, $post)
		{
			if ($post->post_type == 'pdandroidfcm') {
				//unset($actions['edit']);
				//unset($actions['view']);
				//unset($actions['trash']);
				unset($actions['inline hide-if-no-js']);
				return $actions;
			} else {
				return $actions;
			}
		}

		public function bulk_action($actions)
		{
			$actions['send_fcm_notification'] = 'Send FCM Notification';
			return $actions;
		}

		public function pdandroidfcm_cpt()
		{

			$post_type = 'pdandroidfcm';

			$labels = [
				'name' 					=> __('All Registered Devices'),
				'singular_name' 		=> __('Device'),
				'add_new'				=> __('Add New Device'),
				'add_new item'			=> __('Add New Device'),
				'search_items' 			=> __('Search for Email'),
				'edit_item'				=> __('Edit Device'),
				'new_item' 				=> __('Device'),
				'menu_name'				=> __('pd Android FCM'),
				'all_items'          	=> __('All Devices'),
				'name_custom_bar'		=> __('Device'),
				'not_found'           	=> __('No device(s) found'),
				'not_found_in_trash'  	=> __('No device(s) found in Trash')
			];

			$args = [
				'labels'				=> $labels,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'hierarchical'			=> false,
				'menu_position'			=> 27,
				'public'				=> false,
				'has_archive'			=> false,
				'publicaly_querable'	=> false,
				'query_var'				=> false,
				'supports'				=> false,
				'menu_icon' 			=> 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDIwIDIwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyMCAyMDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCgkuc3Qwe2ZpbGw6I2ZmZmZmZjt9DQo8L3N0eWxlPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xLjA3LDYuOTdjMC4yOSwwLDAuNTQsMC4xMSwwLjc1LDAuMzFzMC4zMSwwLjQ2LDAuMzEsMC43NXY0LjQ2YzAsMC4zLTAuMSwwLjU1LTAuMzEsMC43Ng0KCQkJYy0wLjIxLDAuMjEtMC40NSwwLjMxLTAuNzUsMC4zMXMtMC41NS0wLjExLTAuNzYtMC4zMUMwLjExLDEzLjAzLDAsMTIuNzgsMCwxMi40OFY4LjAyYzAtMC4yOSwwLjExLTAuNTQsMC4zMS0wLjc1DQoJCQlDMC41Miw3LjA3LDAuNzcsNi45NywxLjA3LDYuOTd6IE05LjY1LDIuOTdjMC43NCwwLjM4LDEuMzMsMC45MSwxLjc3LDEuNTljMC40NCwwLjY4LDAuNjYsMS40MiwwLjY2LDIuMjNIMi41DQoJCQljMC0wLjgxLDAuMjItMS41NSwwLjY2LTIuMjNDMy42MSwzLjg4LDQuMiwzLjM1LDQuOTQsMi45N0w0LjIxLDEuNjFDNC4xNiwxLjUxLDQuMTcsMS40NSw0LjI2LDEuNGMwLjA5LTAuMDQsMC4xNi0wLjAyLDAuMjEsMC4wNg0KCQkJbDAuNzUsMS4zN0M1Ljg2LDIuNTQsNi41NiwyLjQsNy4yOSwyLjRzMS40MywwLjE1LDIuMDgsMC40M2wwLjc1LTEuMzdjMC4wNS0wLjA4LDAuMTItMC4xMSwwLjIxLTAuMDYNCgkJCWMwLjA4LDAuMDUsMC4xLDAuMTIsMC4wNSwwLjIxTDkuNjUsMi45N3ogTTEyLjA1LDcuMTZ2Ni45YzAsMC4zMi0wLjExLDAuNTktMC4zMywwLjgxYy0wLjIyLDAuMjItMC40OSwwLjMzLTAuOCwwLjMzaC0wLjc4djIuMzUNCgkJCWMwLDAuMy0wLjExLDAuNTUtMC4zMSwwLjc2Yy0wLjIxLDAuMjEtMC40NiwwLjMxLTAuNzYsMC4zMXMtMC41NS0wLjExLTAuNzYtMC4zMWMtMC4yMS0wLjIxLTAuMzEtMC40Ni0wLjMxLTAuNzZWMTUuMkg2LjU5djIuMzUNCgkJCWMwLDAuMy0wLjExLDAuNTUtMC4zMSwwLjc2Yy0wLjIxLDAuMjEtMC40NiwwLjMxLTAuNzYsMC4zMWMtMC4yOSwwLTAuNTQtMC4xMS0wLjc1LTAuMzFjLTAuMjEtMC4yMS0wLjMxLTAuNDYtMC4zMS0wLjc2DQoJCQlMNC40NSwxNS4ySDMuNjljLTAuMzIsMC0wLjU5LTAuMTEtMC44MS0wLjMzYy0wLjIyLTAuMjItMC4zMy0wLjQ5LTAuMzMtMC44MXYtNi45SDEyLjA1eiBNNC44Myw0Ljk0QzQuOTEsNS4wMiw1LDUuMDYsNS4xMSw1LjA2DQoJCQljMC4xMSwwLDAuMjEtMC4wNCwwLjI4LTAuMTJjMC4wOC0wLjA4LDAuMTItMC4xNywwLjEyLTAuMjhTNS40Nyw0LjQ1LDUuMzksNC4zN0M1LjMxLDQuMjksNS4yMiw0LjI1LDUuMTEsNC4yNQ0KCQkJQzUsNC4yNSw0LjksNC4yOSw0LjgzLDQuMzdDNC43NSw0LjQ1LDQuNzIsNC41NCw0LjcyLDQuNjVTNC43NSw0Ljg2LDQuODMsNC45NHogTTkuMiw0Ljk0YzAuMDgsMC4wOCwwLjE3LDAuMTIsMC4yOCwwLjEyDQoJCQljMC4xMSwwLDAuMi0wLjA0LDAuMjgtMC4xMmMwLjA4LTAuMDgsMC4xMS0wLjE3LDAuMTEtMC4yOFM5Ljg0LDQuNDUsOS43Niw0LjM3UzkuNTksNC4yNSw5LjQ4LDQuMjVTOS4yNyw0LjI5LDkuMiw0LjM3DQoJCQlDOS4xMiw0LjQ1LDkuMDgsNC41NCw5LjA4LDQuNjVTOS4xMiw0Ljg2LDkuMiw0Ljk0eiBNMTQuNTksOC4wMnY0LjQ2YzAsMC4zLTAuMTEsMC41NS0wLjMxLDAuNzZjLTAuMjEsMC4yMS0wLjQ2LDAuMzEtMC43NiwwLjMxDQoJCQljLTAuMjksMC0wLjU0LTAuMTEtMC43NS0wLjMxYy0wLjIxLTAuMjEtMC4zMS0wLjQ2LTAuMzEtMC43NlY4LjAyYzAtMC4zLDAuMTEtMC41NSwwLjMxLTAuNzVjMC4yMS0wLjIxLDAuNDYtMC4zMSwwLjc1LTAuMzENCgkJCWMwLjMsMCwwLjU1LDAuMSwwLjc2LDAuMzFDMTQuNDksNy40NywxNC41OSw3LjczLDE0LjU5LDguMDJ6Ii8+DQoJPC9nPg0KCTxnPg0KCQk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTIuNDgsNC4wMWMwLjA0LTAuMDIsMC4wNy0wLjA0LDAuMTEtMC4wNGw3LjE0LTAuNzZjMC4xLTAuMDEsMC4xNywwLjAyLDAuMjIsMC4xMWMwLjA1LDAuMDksMC4wNSwwLjE3LDAsMC4yNQ0KCQkJbC0wLjg0LDEuMzZsLTUuMjYtMC40NGw0Ljk0LDEuM2wwLjc3LDEuMDVjMC4wMywwLjA0LDAuMDUsMC4wOSwwLjA1LDAuMTRzLTAuMDEsMC4xLTAuMDQsMC4xNGMtMC4wMSwwLjAyLTAuMDMsMC4wNC0wLjA1LDAuMDYNCgkJCWMtMC4wNiwwLjA1LTAuMTMsMC4wNi0wLjIsMC4wNGwtMS4zOC0wLjM2bC0wLjk2LDEuNTZjLTAuMDIsMC4wMy0wLjA0LDAuMDUtMC4wNiwwLjA3Yy0wLjAzLDAuMDItMC4wNywwLjA0LTAuMTEsMC4wNQ0KCQkJYy0wLjA4LDAuMDEtMC4xNC0wLjAxLTAuMi0wLjA2bC00LjE1LTQuMDdjLTAuMDctMC4wNy0wLjA5LTAuMTYtMC4wNi0wLjI1QzEyLjQxLDQuMDgsMTIuNDQsNC4wNCwxMi40OCw0LjAxeiIvPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K',
				'taxonomies'			=> array('subscriptions'),
				'capabilities' => array(
					'create_posts' 		=> false
				),
				'map_meta_cap'        	=> true
			];

			if (current_user_can('manage_woocommerce') || current_user_can('activate_plugins')) {
				register_post_type($post_type, $args);
			}
		}

		public function pdandroidfcm_send_push_notification_cpt()
		{

			$post_type = 'pdandroidfcm_msg';

			$labels = [
				'name' 					=> __('Notifications'),
				'singular_name' 		=> __('Notification'),
				'add_new'				=> __('Create New Notification Message'),
				'add_new item'			=> __('Create New Notification Message'),
				'search_items' 			=> __('Search for Sent Notification'),
				'edit_item'				=> __('Edit Notification'),
				'new_item' 				=> __('Notification'),
				'menu_name'				=> __('Push Notifications'),
				'name_custom_bar'		=> __('Notifications '),
				'not_found'           	=> __('No Notification(s) found'),
				'not_found_in_trash'  	=> __('No Notification(s) found in Trash')
			];

			$args = [
				'labels'				=> $labels,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'hierarchical'			=> false,
				'public'				=> false,
				'has_archive'			=> false,
				'publicaly_querable'	=> false,
				'query_var'				=> false,
				'menu_position'			=> 28,
				'rewrite' => array('slug' => 'item', 'with_front' => false,),
				'supports'				=> array('title', 'excerpt', 'featured_image', 'thumbnail'),
				'show_in_menu' 			=> 'edit.php?post_type=pdandroidfcm',
				'taxonomies'			=> array('subscriptions')
			];

			if (current_user_can('manage_woocommerce') || current_user_can('activate_plugins')) {
				register_post_type($post_type, $args);
			}
		}
    
    

    
    public function pdandroidfcm_send_push_notification_cpt_individual()
		{

			$post_type = 'pdandroidfcm_user';

			$labels = [
				'name' 					=> __('Individual Notifications'),
				'singular_name' 		=> __('Individual Notification'),
				'add_new'				=> __('Create New Notification Individual Message'),
				'add_new item'			=> __('Create New Notification Individual Message'),
				'search_items' 			=> __('Search for Sent Individual Notification'),
				'edit_item'				=> __('Edit Individual Notification'),
				'new_item' 				=> __('Individual Notification'),
				'menu_name'				=> __('Push Individual'),
				'name_custom_bar'		=> __('Individual Notifications '),
				'not_found'           	=> __('No Individual Notification(s) found'),
				'not_found_in_trash'  	=> __('No Individual Notification(s) found in Trash')
			];

			$args = [
				'labels'				=> $labels,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'hierarchical'			=> false,
				'public'				=> false,
				'has_archive'			=> false,
				'publicaly_querable'	=> false,
				'query_var'				=> false,
				'menu_position'			=> 29,
				'rewrite' => array('slug' => 'item', 'with_front' => false,),
				'supports'				=> array('title', 'excerpt', 'featured_image', 'thumbnail','author'),
				'show_in_menu' 			=> 'edit.php?post_type=pdandroidfcm_user',
			];

			if (current_user_can('manage_woocommerce') || current_user_can('activate_plugins')) {
				register_post_type($post_type, $args);
			}
		}

		public function create_subscriptions_hierarchical_taxonomy()
		{

			$labels = [
				'name' 					=> _x('Subscriptions', 'taxonomy general name'),
				'singular_name' 		=> _x('Subscription', 'taxonomy singular name'),
				'search_items' 			=>  __('Search Subscriptions'),
				'all_items' 			=> __('All Subscriptions'),
				'parent_item' 			=> __('Parent Subscription'),
				'parent_item_colon' 	=> __('Parent Subscription:'),
				'edit_item' 			=> __('Edit Subscription'),
				'update_item' 			=> __('Update Subscription'),
				'add_new_item' 			=> __('Add New Subscription'),
				'new_item_name' 		=> __('New Subscription Name'),
				'menu_name' 			=> __('Subscriptions'),
			];

			register_taxonomy(
				'subscriptions',
				[],
				[
					'hierarchical' 		=> true,
					'parent_item'  		=> false,
					'parent_item_colon' => false,
					'labels' 			=> $labels,
					'show_ui' 			=> true,
					'show_admin_column' => true,
					'show_in_rest' 		=> true,
					'query_var' 		=> true,
					'rewrite' 			=> array('slug' => 'subscriptions'),
				]
			);
		}

		public function create_pdandroidfcm_table()
		{
			require_once plugin_dir_path(__FILE__) . 'db.php';
		}

		public function pd_fcm_js_scripts()
		{
?>
			<script>
				let generatePdFcmApi = document.getElementById('generatePdFcmApi');
				if (generatePdFcmApi) {
					generatePdFcmApi.addEventListener('click', function(e) {
						e.preventDefault();
						let ajaxUrl = '<?php echo admin_url("admin-ajax.php") ?>';
						let xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								let StringResponse = xhttp.responseText;
								let jsonResponse = JSON.parse(StringResponse);
								if (jsonResponse.success === true) {
									document.getElementById("PD_FCM_API_SECRET_KEY").value = jsonResponse.data;
								}
							}
						}
						let params = '?action=generate_pd_fcm_api';
						xhttp.open("GET", ajaxUrl + params, true);
						xhttp.send();
					});
				}
				const copy_url = (element) => {
					var text = document.querySelector(element);
					var selection = window.getSelection();
					var range = document.createRange();
					range.selectNodeContents(text);
					selection.removeAllRanges();
					selection.addRange(range);
					document.execCommand('copy');
				};
				document.getElementById('PD_FCM_ENVIRONMENT').addEventListener('change', function(e) {
					document.getElementById('PD_FIREBASE_API_KEY').closest('tr').style.display = 'none';
				});
			</script>
<?php
		}

		public function pdandroidfcm_plugin_add_settings_link($actions, $plugin_file)
		{
			static $plugin;

			if (!isset($plugin))

				$plugin = plugin_basename(__FILE__);

			if ($plugin == $plugin_file) {

				$settings 	= ['settings' => '<a href="edit.php?post_type=pdandroidfcm&page=settings">' . __('Settings') . '</a>'];
				$docs_link 	= ['docs' => '<a href="https://proficientdesigners.in/creations/pd-android-fcm-push-notification/" target="_blank">' . __('Documentation') . '</a>'];

				$actions = array_merge($settings, $actions);
				$actions = array_merge($docs_link, $actions);
			}

			return $actions;
		}
	}
	$pdandroidfcm = new PDANDROIDFCM();
}

add_filter( 'wp_dropdown_users_args', 'change_user_dropdown', 10, 2 );

function change_user_dropdown( $query_args, $r ){
    // get screen object
global $wp_roles;
global $wpdb;
	 $screen = get_current_screen();

    // list users whose role is e.g. 'Editor' for 'post' post type
    if( $screen->post_type == 'pdandroidfcm_user' ):
		 $query_args['fields'][0]['ID']  = 1;
        $users_by_key = $wpdb->get_results('SELECT `wp_posts`.`post_author` FROM `wp_pd_android_fcm` INNER JOIN `wp_posts` WHERE `wp_posts`.`ID`=`wp_pd_android_fcm`.`post_ID`');
   echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';
	echo '<select name="post_author_override" id="post_author_override" class="js-example-basic-single">';
                 foreach($users_by_key as $user_by_key){
            		 $user = $wpdb->get_results('SELECT * FROM `wp_users` WHERE `ID`='.$user_by_key->post_author);
           	
            	echo '<option value="'.$user[0]->ID.'">'.$user[0]->display_name.' - '.$user[0]->user_login.'</option>';
            }  
echo '</select>';

echo '<script>jQuery(".js-example-basic-single").select2();</script>';
        // unset default role ;
        unset( $query_args['who'] );
		unset( $query_args['orderby'] );
		unset( $query_args['blog_id'] );
		unset( $query_args['order'] );
		// unset( $query_args['fields'] );
		unset( $query_args['capability'] );

    endif;

    

    return $query_args;
}


// The shortcode function
function wpb_demo_shortcode_2()
{

global $current_user;
get_currentuserinfo();
$user_id = $current_user->ID; 
	$args = array(
		'post_type'     => 'pdandroidfcm_msg',
    	// 'post_status' => array('publish')
	);
	$my_posts = get_posts($args);

	$args_user = array(
		'post_type'     => 'pdandroidfcm_user',
    	'author' => $user_id,
        // 'post_status' => array('publish')
	);
	$my_posts_user = get_posts($args_user);

	

		$output = '';

		$output .= '<div class="tab-ws">
						<button class="tablinks" id="1_tab" onclick="openCity(event, 1)">Public</button>
  						<button class="tablinks" id="2_tab" onclick="openCity(event, 2)">Private</button>
					</div>';

		$output .= '<div id="1" class="tabcontent-ws">';
		foreach ($my_posts as $p) {

			$image = wp_get_attachment_image_src(get_post_thumbnail_id($p->ID));
			$output .= '<a href="' . get_post_permalink($p->ID) . '"><div class="card mt-4"><div class=" m-0 p-2 ws-row ">';
			$output .= '<h4 class="mobile-view-ws"><b>' . $p->post_title . '</b></h4>';
			if ($image) {
				$output .= '<div class="column web_ws_image"><img src="' . $image[0] . '" class="pr-3" style="width:100%"></div>';
			}




			$output .= '<div class="column">
			<h4 class="web-view-ws"><b>' . $p->post_title . '</b></h4>
				<p>' . substr($p->post_excerpt, 0, 200) . '</p>
				<p style="margin: 0;
				/* position: absolute; */
				margin-top: 61px;">' . date('Y-m-d h:i A', strtotime($p->post_date)) . '</p>
			</div>
			</div></div></a>';

				}
		$output .= '</div>';

		$output .= '<div id="2" class="tabcontent-ws">';
  					foreach ($my_posts_user as $p2) {

			$imagep2 = wp_get_attachment_image_src(get_post_thumbnail_id($p2->ID));
                   
			$output .= '<a href="' . get_post_permalink($p2->ID) . '"><div class="card mt-4"><div class=" m-0 p-2 ws-row ">';
			$output .= '<h4 class="mobile-view-ws"><b>' . $p2->post_title . '</b></h4>';
			if ($image) {
				$output .= '<div class="column web_ws_image"><img src="' . $imagep2[0] . '" class="pr-3" style="width:100%"></div>';
			}




			$output .= '<div class="column">
			<h4 class="web-view-ws"><b>' . $p2->post_title . '</b></h4>
				<p>' . substr($p2->post_excerpt, 0, 200) . '</p>
				<p style="margin: 0;
				/* position: absolute; */
				margin-top: 61px;">' . date('Y-m-d h:i A', strtotime($p2->post_date)) . '</p>
			</div>
			</div></div></a>';

				}
					$output .= '</div>';

$output .= '<style>
			@media only screen and (max-width: 600px) {
				.web-view-ws{display:none;}
			}
			@media only screen and (min-width: 992px) {
			.ws-row{
				display: -ms-flexbox;
				display: flex;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
				margin-right: -15px;
				margin-left: -15px;
			}
			.mobile-view-ws{display:none;}
			}
				/* Style the tab */
.tab-ws {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons that are used to open the tab content */
.tab-ws button {
  //background-color: inherit;
  float: left;
  border: none;
  outline: none;
  width:50%;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab-ws button:hover {
  background-color: #00008b;
}

/* Create an active/current tablink class */
.tab-ws button.active {
  background-color: #00008b;
}

/* Style the tab content */
.tabcontent-ws {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
			</style>
<script>
function openCity(evt, cityName) {
  			// Declare all variables
  		var i, tabcontent, tablinks;

  		// Get all elements with class="tabcontent" and hide them
  		tabcontent = document.getElementsByClassName("tabcontent-ws");
  		for (i = 0; i < tabcontent.length; i++) {
    	tabcontent[i].style.display = "none";
  		}

  		// Get all elements with class="tablinks" and remove the class "active"
 		 tablinks = document.getElementsByClassName("tablinks");
  		for (i = 0; i < tablinks.length; i++) {
    	tablinks[i].className = tablinks[i].className.replace(" active", "");
  		}

  		// Show the current tab, and add an "active" class to the button that opened the tab
  		document.getElementById(cityName).style.display = "block";
  		evt.currentTarget.className += " active";
		}
        
        
        function openCityload(evt, cityName) {
  			// Declare all variables
  		var i, tabcontent, tablinks;

  		// Get all elements with class="tabcontent" and hide them
  		tabcontent = document.getElementsByClassName("tabcontent-ws");
  		for (i = 0; i < tabcontent.length; i++) {
    	tabcontent[i].style.display = "none";
  		}

  		// Get all elements with class="tablinks" and remove the class "active"
 		 tablinks = document.getElementsByClassName("tablinks");
  		for (i = 0; i < tablinks.length; i++) {
    	tablinks[i].className = tablinks[i].className.replace(" active", "");
  		}

  		// Show the current tab, and add an "active" class to the button that opened the tab
  		document.getElementById(cityName).style.display = "block";
  		var d = document.getElementById("1_tab");
		d.className += " active";
		}
        
        
        window.onload = openCityload(event, 1);

</script>';
		
		
	

	echo $output;
}
// Register shortcode
add_shortcode('my_ad_code_ws', 'wpb_demo_shortcode_2');

// activation



add_filter('woocommerce_account_menu_items', 'misha_one_more_link');
function misha_one_more_link($menu_links)
{

	// we will hook "anyuniquetext123" later
	$new = array('anyuniquetext123' => 'Notifications');

	// or in case you need 2 links
	// $new = array( 'link1' => 'Link 1', 'link2' => 'Link 2' );

	// array_slice() is good when you want to add an element between the other ones
	$menu_links = array_slice($menu_links, 0, 1, true)
		+ $new
		+ array_slice($menu_links, 1, NULL, true);


	return $menu_links;
}

add_filter('woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 4);


function print_post_title()
{
	global $post;
	$thePostID = $post->ID;
	$post_id = get_post($thePostID);
	$title = $post_id->post_title;
	$perm = get_permalink($post_id);
	$post_keys = array();
	$post_val = array();
	$post_keys = get_post_custom_keys($thePostID);

	if (!empty($post_keys)) {
		foreach ($post_keys as $pkey) {
			if ($pkey == 'external_url') {
				$post_val = get_post_custom_values($pkey);
			}
		}
		if (empty($post_val)) {
			$link = $perm;
		} else {
			$link = $post_val[0];
		}
	} else {
		$link = $perm;
	}
	echo '<h2><a href="' . $link . '" rel="bookmark" title="' . $title . '">' . $title . '</a></h2>';
}

function misha_hook_endpoint($url, $endpoint, $value, $permalink)
{

	if ($endpoint === 'anyuniquetext123') {

		// ok, here is the place for your custom URL, it could be external
		$url = site_url();
	}
	return $url . '/notifications';
}
register_activation_hook(__FILE__, [$pdandroidfcm, 'activate']);


add_action('woocommerce_new_order', 'ws8woocommerce_new_order_notity', 40, 1);
function ws8OrderPlacedTriggerSomething($order_id)
{
}

add_action('woocommerce_order_status_completed', 'ws8woocommerce_order_status_completed_notity', 11, 1);
function ws8woocommerce_order_status_completed_notity($order_id)
{
global $wpdb;
	$users_notify_list = $wpdb->get_results('SELECT `user_id`,`amount` FROM `wp_transactions` WHERE `transaction_code`='.$order_id.' AND `user_id` IS NOT NULL GROUP BY `user_id`');
    /*creating firebase class object */
	$order_user = wc_get_order( $order_id )->get_user_id();
	$firebase = new PDANDROIDFCMFirebase();
	$title = 'You have earned';

   foreach($users_notify_list as $user_notify){
   		
    	$_2Plans = $wpdb->get_results('SELECT a.transaction_type_id, b.transaction_name, sum(a.`amount`) as total FROM `wp_transactions` AS a INNER JOIN wp_transaction_types as b on a.transaction_type_id = b.id WHERE a.`user_id`='.$user_notify->user_id.' AND a.transaction_status_id= 1 GROUP BY transaction_type_id');
		$Binary = $wpdb->get_results('SELECT * FROM `wp_isa_point_log` WHERE order_bill_id='.$order_id.'  ORDER BY `id` DESC');
		foreach($_2Plans as $_2Plan){
			if($_2Plan->transaction_type_id == 1){
				$Decade = $_2Plan->total;
			} elseif($_2Plan->transaction_type_id == 11){
				$IsimagaShare = $_2Plan->total;
			}
		}
        
   
   		$message ='You have earned Rs.'.$user_notify->amount.' for this order ('.$order_id.').Your have total pending earning (Decade Plan Rs.'.$Decade.'), (iShare Plan Rs.'.$IsimagaShare.'), (Binary Plan Rs. _ _ _), (Points Plan Rs. _ _ _), (UTL Plan Rs. _ _ _)';
   		
   		
  		$terms = $wpdb->get_results('SELECT * FROM `wp_posts` WHERE `post_author`='.$user_notify->user_id.' AND `post_type` = "pdandroidfcm" AND `post_status` = "publish" LIMIT 1');
   		$terms = reset($terms);
   		if($terms){
   			//$firebase->send(pdandroidfcm_get_column_data($terms->ID, 'device_token'), $res);
        	$new_post = array(
            	'post_title' => $title,
            	'post_excerpt' => $message,
            	'post_status' => 'publish',
            	'post_author' => $user_notify->user_id,
            	'post_type' => 'pdandroidfcm_user',
            	'post_category' => array(0),
			);
        
        	
			$post_id = wp_insert_post($new_post);
   		}
   		
   }
   
    
}


add_action( 'save_post', 'pdandroidfcm_user_send_push', 20, 1 );
function pdandroidfcm_user_send_push( $post_id ) {
    // this should be the updated post object 
global $wpdb;
$firebase = new PDANDROIDFCMFirebase();
$post = get_post( $post_id );
if($post->post_type == 'pdandroidfcm_user' && get_post_status( $post_id )=='publish'){

 $terms = $wpdb->get_results('SELECT * FROM `wp_posts` WHERE `post_author`='.$post->post_author.' AND `post_type` = "pdandroidfcm" AND `post_status` = "publish" LIMIT 1');
   		$terms = reset($terms);

   		if($terms){
	$res = array();
        	$res['data']['title'] = get_the_title( $post_id );;
        	$res['data']['body'] =  get_the_excerpt($post_id);
        	$res['data']['image'] = wp_get_attachment_image_src(get_post_thumbnail_id($post_id))[0];
 
   			$firebase->send(pdandroidfcm_get_column_data($terms->ID, 'device_token'), $res);
        }
}
  
}

add_action('woocommerce_payment_complete', 'ws8woocommerce_payment_complete_notity', 40, 1);
function ws8woocommerce_payment_complete_notity($order_id)
{
}



/*----------------------------------------------------*/

foreach (glob(plugin_dir_path(__FILE__) . "ajax/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "custom/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "class/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "wp_api/*.php") as $file) {
	include $file;
}

foreach (glob(plugin_dir_path(__FILE__) . "functions/*.php") as $file) {
	include $file;
}

require plugin_dir_path(__FILE__) . 'pages.php';