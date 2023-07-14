<?php

add_action('init', 'pdfcm_android_getting_cpt_chosen_list_to_add_custom_tax', 10, 1);

function pdfcm_android_getting_cpt_chosen_list_to_add_custom_tax()
{
	$blog_opt = get_option('PD_BLOG_AS_NOTIF') ? get_option('PD_BLOG_AS_NOTIF') : '';
	if ($blog_opt) {
		register_taxonomy_for_object_type('subscriptions', 'post');
	}

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
			if ($checked == 'checked') {
				register_taxonomy_for_object_type('subscriptions', $pre_registered_cpt);
			}
		}
	}
}
