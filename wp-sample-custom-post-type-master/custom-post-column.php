<?php

/**
 * Modify which columns display in the admin views
 * --> manage_{post type}_posts_columns
 */
add_filter('manage_cpt_post_type_posts_columns', 'cpt_post_type_posts_columns');
function cpt_post_type_posts_columns($posts_columns) 
{
	$tmp = array();

	$tmp['cb'] = '<input type="checkbox" />';
	$tmp['title'] = "Fund Name";

	$tmp['web'] = "Fund Web";

	$tmp['g_1y'] = "High Growth/Growth - 1 Year";
	$tmp['g_3y'] = "High Growth/Growth - 3 Year";
	$tmp['g_5y'] = "High Growth/Growth - 5 Year";
	$tmp['g_7y'] = "High Growth/Growth - 7 Year";
	$tmp['g_10y'] = "High Growth/Growth - 10 Year";

	$tmp['p_1y'] = "Property - 1 Year";
	$tmp['p_3y'] = "Property - 3 Year";
	$tmp['p_5y'] = "Property - 5 Year";
	$tmp['p_7y'] = "Property - 7 Year";
	$tmp['p_10y'] = "Property - 10 Year";

	$tmp['b_1y'] = "Balanced - 1 Year";
	$tmp['b_3y'] = "Balanced - 3 Year";
	$tmp['b_5y'] = "Balanced - 5 Year";
	$tmp['b_7y'] = "Balanced - 7 Year";
	$tmp['b_10y'] = "Balanced - 10 Year";

	$tmp['c_1y'] = "Cash - 1 Year";
	$tmp['c_3y'] = "Cash - 3 Year";
	$tmp['c_5y'] = "Cash - 5 Year";
	$tmp['c_7y'] = "Cash - 7 Year";
	$tmp['c_10y'] = "Cash - 10 Year";
	
	$tmp['i_1y'] = "International Shares - 1 Yearsr";
	$tmp['i_3y'] = "International Shares - 3 Years";
	$tmp['i_5y'] = "International Shares - 5 Years";
	$tmp['i_7y'] = "International Shares - 7 Years";
	$tmp['i_10y'] = "International Shares - 10 Years";

	$tmp['a_1y'] = "Australian Shares - 1 Year";
	$tmp['a_3y'] = "Australian Shares - 3 Year";
	$tmp['a_5y'] = "Australian Shares - 5 Year";
	$tmp['a_7y'] = "Australian Shares - 7 Year";
	$tmp['a_10y'] = "Australian Shares - 10 Year";


	$tmp['date'] = 'Date';

	return $tmp;
}


/**
 * Custom column output when admin is viewing the post type
 * --> manage_{post type}_posts_custom_column
 */
add_action('manage_cpt_post_type_posts_custom_column', 'cpt_post_type_posts_custom_column');
function cpt_post_type_posts_custom_column($column_name) 
{
	global $post;
	
	
	if($column_name == 'web'){
		echo "<i>empty</i>";
	}else if($column_name == 'g_1y'){
		echo "<i>empty</i>";
	}else if($column_name == 'g_3y'){
		echo "<i>empty</i>";
	}else if($column_name == 'g_5y'){
		echo "<i>empty</i>";
	}else if($column_name == 'g_7y'){
		echo "<i>empty</i>";
	}else if($column_name == 'g_10y'){
		echo "<i>empty</i>";
	}else if($column_name == 'p_1y'){
		echo "<i>empty</i>";
	}else if($column_name == 'p_3y'){
		echo "<i>empty</i>";
	}else if($column_name == 'p_5y'){
		echo "<i>empty</i>";
	}else if($column_name == 'p_7y'){
		echo "<i>empty</i>";
	}else if($column_name == 'p_10y'){
		echo "<i>empty</i>";
	}else if($column_name == 'b_1y'){
		echo "<i>empty</i>";
	}else if($column_name == 'b_3y'){
		echo "<i>empty</i>";
	}else if($column_name == 'b_5y'){
		echo "<i>empty</i>";
	}else if($column_name == 'b_7y'){
		echo "<i>empty</i>";
	}else if($column_name == 'b_10y'){
		echo "<i>empty</i>";
	}else if($column_name == 'c_1y'){
		echo "<i>empty</i>";
	}else if($column_name == 'c_3y'){
		echo "<i>empty</i>";
	}else if($column_name == 'c_5y'){
		echo "<i>empty</i>";
	}else if($column_name == 'c_7y'){
		echo "<i>empty</i>";
	}else if($column_name == 'c_10y'){
		echo "<i>empty</i>";
	}else if($column_name == 'i_1y'){
		echo "<i>empty</i>";
	}else if($column_name == 'i_3y'){
		echo "<i>empty</i>";
	}else if($column_name == 'i_5y'){
		echo "<i>empty</i>";
	}else if($column_name == 'i_7y'){
		echo "<i>empty</i>";
	}else if($column_name == 'i_10y'){
		echo "<i>empty</i>";
	}else if($column_name == 'a_1y'){
		echo "<i>empty</i>";
	}else if($column_name == 'a_3y'){
		echo "<i>empty</i>";
	}else if($column_name == 'a_5y'){
		echo "<i>empty</i>";
	}else if($column_name == 'a_7y'){
		echo "<i>empty</i>";
	}else if($column_name == 'a_10y'){
		echo "<i>empty</i>";
	}
	
}


/**
 * Sorting custom column 
 * --> manage_edit-{post type}_sortable_columns
 */
add_filter( 'manage_edit-cpt_post_type_sortable_columns', 'cpt_post_type_sortable_columns' );
function cpt_post_type_sortable_columns( $columns ) 
{
    $columns['cpt_input_text'] = 'cpt_input_text';
 
    //To make a column 'un-sortable' remove it from the array
    //unset($columns['date']);
 
    return $columns;
}


/**
 * Enable custom post meta filter sorting
 */
add_filter( 'request', 'filter_column_orderby' );
function filter_column_orderby( $vars ) 
{
	if ( isset( $vars['orderby'] ) && 'cpt_input_text' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'cpt_input_text',
			'orderby' => 'meta_value'
		) );
	}

	return $vars;
}