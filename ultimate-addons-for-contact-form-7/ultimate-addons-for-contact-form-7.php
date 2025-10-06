<?php
/**
 * Plugin Name: Ultra Addons for Contact Form 7
 * Plugin URI: https://cf7addons.com/
 * Description: 50+ Essential Addons for Contact Form 7 - Conditional Fields, Multi Step Forms, Redirection, Form Templates, Columns, WooCommerce, Mailchimp and more, all in one.
 * Version: 3.5.30
 * Author: Themefic
 * Author URI: https://themefic.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ultimate-addons-cf7
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Class Ultimate_Addon_CF7
 */
class Ultimate_Addons_CF7 {

	/*
	 * Construct a function
	 */
	public function __construct() {
		define( 'UACF7_FILE', __FILE__ );
		define( 'UACF7_URL', plugin_dir_url( __FILE__ ) );
		define( 'UACF7_ADDONS', UACF7_URL . 'addons' );
		define( 'UACF7_PATH', plugin_dir_path( __FILE__ ) );

		define( 'UACF7_VERSION', '3.5.30' );

		if ( ! class_exists( 'Appsero\Client' ) ) {
			require_once( __DIR__ . '/inc/app/src/Client.php' );
		}

		//Plugin loaded
		add_action( 'init', [ $this, 'uacf7_plugin_loaded' ], 9 );

		if ( defined( 'WPCF7_VERSION' ) && WPCF7_VERSION >= 5.7 ) {
			add_filter( 'wpcf7_autop_or_not', '__return_false' );
		}

		register_activation_hook(__FILE__, array($this,'uacf7_plugin_activation'));

		//enqueue scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'tf_tourfic_admin_denqueue_script' ], 20 );
	}

	/*
	 * Ultimate addons loaded
	 */
	public function uacf7_plugin_loaded() {
		//Register text domain
		load_plugin_textdomain( 'ultimate-addons-cf7', false, basename( dirname( __FILE__ ) ) . '/languages' );

		// Initialize the appsero
		$this->appsero_init_tracker_ultimate_addons_for_contact_form_7();

		//Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'uacf7_frontend_scripts' ) );

		//Require ultimate functions
		require_once( 'inc/functions.php' );


		if ( class_exists( 'WPCF7' ) ) {
			//Init ultimate addons
			$this->uacf7_init();

		} else {
			//Admin notice
			add_action( 'admin_notices', array( $this, 'uacf7_admin_notice' ) );
		}


		// Require the main Option file
		if ( file_exists( UACF7_PATH . 'admin/tf-options/TF_Options.php' ) ) {
			require_once UACF7_PATH . 'admin/tf-options/TF_Options.php';
		}
	}

	function uacf7_plugin_activation() {
		update_option('uacf7_plugin_last_updated', time());
	}

	/*
	 * Admin setting option dequeue 
	 */
	public function tf_tourfic_admin_denqueue_script( $screen ) {
		$UACF7_options_screens = array(
			'toplevel_page_uacf7_settings',
			'cf7-addons_page_uacf7_addons',
			'toplevel_page_wpcf7',
			'contact_page_wpcf7-new',
			'admin_page_uacf7-setup-wizard',
			'cf7-addons_page_uacf7_license_info',
		);

		//The tourfic admin js Listings Directory Compatibility
		if ( in_array( $screen, $UACF7_options_screens )) {
			wp_dequeue_style( 'tf-admin' );
			wp_deregister_style( 'tf-admin' );
			wp_dequeue_style( 'tf-pro' );
			wp_dequeue_script( 'tf-pro' );
			wp_deregister_script('tf-pro');
		}

	}

	/*
	 * Admin notice- To check the Contact form 7 plugin is installed
	 */
	public function uacf7_admin_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<?php printf(
					__( '%s requires %s to be installed and active. You can install and activate it from %s', 'ultimate-addons-cf7' ),
					'<strong>Ultra Addons for Contact Form 7</strong>',
					'<strong>Contact form 7</strong>',
					'<a href="' . admin_url( 'plugin-install.php?tab=search&s=contact+form+7' ) . '">here</a>.'
				); ?>
			</p>
		</div>
		<?php
	}

	/*
	 * Init ultimate addons
	 */
	public function uacf7_init() {
		//Require admin menu
		require_once UACF7_PATH . 'admin/admin-menu.php';

		//Require ultimate addons
		require_once UACF7_PATH . 'addons/addons.php';



		//  Update UACF7 Plugin Version
		if ( UACF7_VERSION != get_option( 'uacf7_version' ) ) {
			update_option( 'uacf7_version', UACF7_VERSION );
		}

	}


	// Enqueue admin scripts
	public function enqueue_admin_scripts( $screen ) {
		global $post_type;

		$tf_options_screens = array(
			'toplevel_page_uacf7_settings',
			'cf7-addons_page_uacf7_addons',
			'toplevel_page_wpcf7',
			'contact_page_wpcf7-new',
			'admin_page_uacf7-setup-wizard',
			'cf7-addons_page_uacf7_license_info',
		);

		$tf_options_post_type = array( 'uacf7_review' );

		// Ensure is_plugin_active function is available
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Check if the UACF7 pro plugin is active
		$pro_active = is_plugin_active( 'ultimate-addons-for-contact-form-7-pro/ultimate-addons-for-contact-form-7-pro.php' );

		if ( in_array( $screen, $tf_options_screens ) || in_array( $post_type, $tf_options_post_type ) ) {
			wp_enqueue_style( 'uacf7-admin-style', UACF7_URL . 'assets/css/admin-style.css', 'sadf' );

			// // wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'uacf7-admin-script', UACF7_URL . 'assets/js/admin-script.js', array( 'jquery' ), null, true );
			wp_localize_script(
				'uacf7-admin',
				'uacf7_options',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'uacf7_options_nonce' ),
				)
			);

			wp_localize_script('uacf7-admin-script', 'uacf7_admin_data', [
				'uacf7_nonce' => wp_create_nonce('uacf7_admin_nonce'),
				'themefic_nonce' => wp_create_nonce('themefic_plugin_nonce'),
			]);

			wp_localize_script(
				'uacf7-admin',
				'uacf7_admin_params',
				array(
					'uacf7_nonce' => wp_create_nonce( 'updates' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'pro_active' => $pro_active
				)
			);
			
			wp_enqueue_style( 'uacf7-notyf', UACF7_URL . 'assets/app/libs/notyf/notyf.min.css', '', UACF7_VERSION );
			wp_enqueue_script( 'uacf7-notyf', UACF7_URL . 'assets/app/libs/notyf/notyf.min.js', array( 'jquery' ), UACF7_VERSION, true );
		}
	}

	// Enqueue admin scripts
	public function uacf7_frontend_scripts() {
		wp_enqueue_style( 'uacf7-frontend-style', UACF7_URL . 'assets/css/uacf7-frontend.css', '' );
		wp_enqueue_style( 'uacf7-form-style', UACF7_URL . 'assets/css/form-style.css', '' );
	}

	/**
	 * Initialize the plugin tracker
	 *
	 * @return void
	 */
	public function appsero_init_tracker_ultimate_addons_for_contact_form_7() {

		$client = new Appsero\Client( '7d0e21bd-f697-4c80-8235-07b65893e0dd', 'Ultra Addons for Contact Form 7', __FILE__ );

		// Change Admin notice text

		$notice = sprintf( $client->__trans( 'Want to help make <strong>%1$s</strong> even more awesome? Allow %1$s to collect non-sensitive diagnostic data and usage information. I agree to get Important Product Updates & Discount related information on my email from  %1$s (I can unsubscribe anytime).' ), $client->name );
		$client->insights()->notice( $notice );

		// Active insights
		$client->insights()->init();

	}
}

/*
 * Object - Ultimate_Addons_CF7
 */
$ultimate_addons_cf7 = new Ultimate_Addons_CF7();
