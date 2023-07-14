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

@@@@@ Custom meta boxes

***********************************/

function pdandroidfcm_add_meta_box()
{
	//add_meta_box( 'device_details', 'User & Device details', 'pdandroidfcm_device_details_page', 'post', 'normal', 'high' );

	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	/*names or objects, note names is the default*/
	$output = 'objects';
	/*'and' or 'or'*/
	$operator = 'and';

	$post_types = get_post_types( $args, $output, $operator );

	if ($post_types)
	{
		foreach ( $post_types  as $post_type )
		{
			$options = get_option( 'PD_CPT_AS_NOTIF' ) ? get_option( 'PD_CPT_AS_NOTIF' ) : array('null');
			$pre_registered_cpt = $post_type->rewrite['slug'];
			$checked = ( in_array($pre_registered_cpt, $options) ? 'checked' : '' );
			if ($checked == 'checked')
			{
				// adding metabox in cpt
				add_meta_box( 'pd_send_push_notif', 'pd Android FCM', 'pd_send_push_notif_cb', $pre_registered_cpt, 'side', 'high' );
			}
		}
	}
	// adding metabox in blog post
	add_meta_box( 'pd_send_push_notif', 'pd Android FCM', 'pd_send_push_notif_cb', 'post', 'side', 'high' );
}

function pdandroidfcm_device_details_page( $post )
{
	?>

	<table cellpadding="10">
		<tr>
			<td><b>Registered Email:</b></td>
			<td><a href="mailto:<?php echo get_the_title( $post ); ?>"><?php echo get_the_title( $post ); ?></a></td>
		</tr>
		<tr>
			<td><b>Device Name:</b></td>
			<td><?php echo pdandroidfcm_get_column_data($post->ID, 'device') ?></td>
		</tr>
		<tr>
			<td><b>Device Token:</b></td>
			<td><?php echo pdandroidfcm_get_column_data($post->ID, 'device_token') ?></td>
		</tr>
		<tr>
			<td><b>OS Version:</b></td>
			<td><?php echo pdandroidfcm_get_column_data($post->ID, 'os_version') ?></td>
		</tr>
	</table>

	<?php
}

function pd_send_push_notif_cb()
{
	?>
	<label for="pd_send_push_notif_chk">
		<input id="pd_send_push_notif_chk" type="checkbox" name="pd_send_push_notif_chk" checked> Send notification on post publish
	</label>
	<?php
}

/*add_filter('current_screen', 'my_current_screen' );
 
function my_current_screen($screen) {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return $screen;
    echo $screen->id;
    return $screen;
}*/