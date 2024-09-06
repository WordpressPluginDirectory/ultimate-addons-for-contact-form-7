<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Get all the meals from glabal settings
 * @author AbuHena
 * @since 1.7.0
 */

if ( isset( $_GET['post'] ) && ! is_array( $_GET['post'] ) && $_GET['post'] != '-1' && isset( $_GET['page'] ) && $_GET['page'] == 'wpcf7' ) {
	$post_id = $_GET['post'];
} else {
	$post_id = 0;
}
UACF7_Metabox::metabox( 'uacf7_form_opt', array(
	'title' => __( 'Ultimate Addons for CF7 Options', 'ultimate-addons-cf7' ),
	'post_type' => 'uacf7',

	'sections' => apply_filters( 'uacf7_post_meta_options', $value = array(), $post_id ),

) );
