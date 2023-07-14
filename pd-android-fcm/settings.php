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

@@@@@ Settings

 ***********************************/

function pdandroidfcm_settings()
{

	/*---------------@ Reg Form Settings @---------------------*/
	register_setting('pdandroidfcm_fields', 'PD_FCM_ENVIRONMENT');
	register_setting('pdandroidfcm_fields', 'PD_FIREBASE_API_KEY');
	register_setting('pdandroidfcm_fields', 'PD_FCM_API_SECRET_KEY');
	register_setting('pdandroidfcm_fields', 'PD_BLOG_AS_NOTIF');
	register_setting('pdandroidfcm_fields', 'PD_CPT_AS_NOTIF');

	add_settings_section('pdandroidfcm_fields', '', '', 'pdandroidfcm_settings');

	add_settings_field('set-fcm-environment', 'Choose Environment', 'pdandroidfcm_environment', 'pdandroidfcm_settings', 'pdandroidfcm_fields');

	$environment = esc_attr(get_option('PD_FCM_ENVIRONMENT'));
	if ($environment === 'production') {
		add_settings_field('activate-fcm-settings', 'FIREBASE API KEY', 'pdandroidfcm_api_key', 'pdandroidfcm_settings', 'pdandroidfcm_fields');
	}

	add_settings_field('activate-pd-fcm-api', 'pd Android FCM API KEY', 'pdandroid_pd_fcm_local_api_key', 'pdandroidfcm_settings', 'pdandroidfcm_fields');
	add_settings_field('activate-pt-settings', 'Choose if you want to send new Blog Post as Push Notification(s)', 'pdandroidfcm_send_notif_settings_blog', 'pdandroidfcm_settings', 'pdandroidfcm_fields');
	add_settings_field('activate-cpt-settings', 'Choose if you want to send new Custom Post as Push Notification(s)', 'pdandroidfcm_send_notif_settings_cpt', 'pdandroidfcm_settings', 'pdandroidfcm_fields');
	add_settings_field('pd-fcm-doc-link', 'Documentation', 'pdandroidfcm_docs_links', 'pdandroidfcm_settings', 'pdandroidfcm_fields');
}

add_action('admin_init', 'pdandroidfcm_settings');

/*-----------------------------*/

function pdandroidfcm_environment()
{
?>
	<select name="PD_FCM_ENVIRONMENT" id="PD_FCM_ENVIRONMENT">
		<option value="production" <?php selected(esc_attr(get_option('PD_FCM_ENVIRONMENT')), 'production') ?>>Production</option>
		<option value="testing" <?php selected(esc_attr(get_option('PD_FCM_ENVIRONMENT')), 'testing') ?>>Testing</option>
	</select>
<?php
}

function pdandroidfcm_api_key()
{
	echo '<input type="text" style="width: 100%" id="PD_FIREBASE_API_KEY" name="PD_FIREBASE_API_KEY" value="' . esc_attr(get_option('PD_FIREBASE_API_KEY')) . '" />';
}

function pdandroid_pd_fcm_local_api_key()
{
	echo '<input type="text" style="width: 40%; margin-right: 15px" id="PD_FCM_API_SECRET_KEY" name="PD_FCM_API_SECRET_KEY" value="' . esc_attr(get_option('PD_FCM_API_SECRET_KEY')) . '" />';
	echo '<button class="button button-default" id="generatePdFcmApi">Generate API Key</button>';
}

function pdandroidfcm_send_notif_settings_blog()
{
	$options = esc_attr(get_option('PD_BLOG_AS_NOTIF'));
	$checked = (@$options == 1 ? 'checked' : '');
	echo '<p><label for="PD_BLOG_AS_NOTIF"><input type="checkbox" id="PD_BLOG_AS_NOTIF" name="PD_BLOG_AS_NOTIF" value="1" ' . $checked . ' /> Blog</label></p>';
}

function pdandroidfcm_send_notif_settings_cpt()
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
			$checked = (in_array($pre_registered_cpt, $options) ? 'checked' : '');
			echo '<p><label for=""><input type="checkbox" id="PD_CPT_AS_NOTIF" name="PD_CPT_AS_NOTIF[]" value="' . $post_type->rewrite['slug'] . '" ' . $checked . '  /> ' . $post_type->label . '</label></p>';
		}
	} else {
		echo "<p>It seems there is no Custom Post Type registered in your site !!</p>";
	}
}

function pdandroidfcm_docs_links()
{
	echo '<p><i>Visit our official plugin page to see <a href="https://proficientdesigners.in/creations/pd-android-fcm-push-notification/" target="_blank">documentation</a></i>.</p>';
}

/*-----------------------------*/

function pdandroidfcm_page()
{ ?>

	<div class="wrap">
		<div class="card" style="max-width: 100%">
			<h2><a>pd Android FCM - Settings</a></h2>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php

				settings_fields('pdandroidfcm_fields');
				do_settings_sections('pdandroidfcm_settings');
				submit_button();

				?>
			</form>
		</div>
	</div>

	<div class="wrap">
		<div class="card" style="max-width: 100%">
			<h2><a>List of API' s to use</a> </h2>
			<table class="form-table">
				<tr>
					<th>Subscribe</th>
					<td>
						<a href="javascript:void(0)" onclick="copy_url('#subscribe_url')" style="text-decoration: none;" title="Copy URL"><span class="dashicons dashicons-admin-page"></span></a>
						<span id="subscribe_url"><?php echo site_url('wp-json/pd/fcm/subscribe'); ?></span>
					</td>
				</tr>
				<tr>
					<th>Un-Subscribe</th>
					<td>
						<a href="javascript:void(0)" onclick="copy_url('#unsubscribe_url')" style="text-decoration: none;" title="Copy URL"><span class="dashicons dashicons-admin-page"></span></a>
						<span id="unsubscribe_url"><?php echo site_url('wp-json/pd/fcm/unsubscribe'); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

<?php }
