<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'uacf7_add_plugin_page' ], 9999 );
	}

	/*
	 * Admin menu
	 */
	public function uacf7_add_plugin_page() {
		add_submenu_page(
			'uacf7_settings', //parent slug
			__( 'Setup Wizard', 'ultimate-addons-cf7' ), // page_title
			__( 'Setup Wizard', 'ultimate-addons-cf7' ), // menu_title
			'manage_options', // capability 
			'admin.php?page=uacf7-setup-wizard', // menu_slug
		);
	}


}

new UACF7_Admin_Menu();
