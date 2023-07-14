<?php
/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.0
 * @param passing data of original text and translated text
 * @description Changing text in this plugin admin pages
 */

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

add_action( 'current_screen', 'pdandroidfcm_this_screen' );

/**
 * Run code on the admin plugin page
 */
function pdandroidfcm_this_screen() {
	$currentScreen = get_current_screen();
	if( $currentScreen->id === "pdandroidfcm_msg" ) {
		add_filter( 'gettext', 'pdandroidfcm_rename_push_btn', 10, 2 );
	}
}

function pdandroidfcm_rename_push_btn( $translation, $original )
{
	if ( 'Update' == $original )
	{
		return 'Update & Send';
	}
	elseif ( 'Publish' == $original )
	{
		return 'Send Message';
	}
	elseif ( 'Excerpt' == $original )
	{
		return 'Your Message';
	}
	else
	{
		$pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
		if ($pos !== false) {
			return '<small><b>Note:</b> <i>(Maximum 100 characters would possibly be visible in the notification shades of the android device)</i></small>';
		}
	}
	return $translation;
}