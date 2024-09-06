<?php
/*
 * Star Review Metabox regiter
 * @author M Hemel Hasan
 */
if ( isset( $_GET['post'] ) && ! is_array( $_GET['post'] ) && $_GET['post'] != '-1' ) {
	$post_id = $_GET['post'];
} else {
	$post_id = 0;
}

UACF7_Metabox::metabox( 'uacf7_review_opt', array(
	'title' => __( 'Ultimate Addons for CF7 Options', 'ultimate-addons-cf7' ),
	'post_type' => 'uacf7_review',
	'sections' => apply_filters( 'uacf7_post_meta_review_opt', $value = array(), $post_id ),

) );

?>