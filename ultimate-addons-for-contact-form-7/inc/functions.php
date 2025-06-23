<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include Classes  
 */
if ( file_exists( UACF7_PATH . 'inc/class-setup-wizard.php' ) ) {
	require_once UACF7_PATH . 'inc/class-setup-wizard.php';
}

//Require ultimate Promo Notice
if ( file_exists( UACF7_PATH . 'inc/class-promo-notice.php' ) ) {

    require_once ( UACF7_PATH .'inc/class-promo-notice.php');
}

//Require ultimate Promo Notice
if ( file_exists( UACF7_PATH . 'inc/class-helper-banner.php' ) ) {

    require_once ( UACF7_PATH .'inc/class-helper-banner.php');
}

if ( file_exists( UACF7_PATH . 'admin/admin-menu.php' ) ) {
	require_once UACF7_PATH . 'admin/admin-menu.php';
}




// Import export
add_filter( 'uacf7_post_meta_options', 'uacf7_post_meta_options_import_export', 100, 2 );
function uacf7_post_meta_options_import_export( $value, $post_id ) {
	if ( ! empty( $value ) ) {

		$import_export = apply_filters( 'uacf7_post_meta_options_import_export_pro', $data = array(
			'title' => __( 'Import/Export', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-file-export',
			'fields' => array(
				'placeholder_heading' => array(
					'id' => 'placeholder_heading',
					'type' => 'heading',
					'label' => __( 'Import/Export', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Import and export all addon settings associated with this form. Please save the form in order to generate the export file.', 'ultimate-addons-cf7' )
				),
				'uacf7_import_export_backup' => array(
					'id' => 'uacf7_import_export_backup',
					'type' => 'backup',
					'form_id' => $post_id,
				),
			),
		), $post_id );
		$value['import_export'] = $import_export;
		return $value;
	}
}

// Uacf7 Import Export File Upload
if ( ! function_exists( 'uacf7_import_export_file_upload' ) ) {
	function uacf7_import_export_file_upload( $imported_file ) {
		// Download the image file
		$qr_logo_image_data = file_get_contents( $imported_file );

		// Create a unique filename for the image
		$qr_logo_filename = basename( $imported_file );
		$qr_logo_upload_dir = wp_upload_dir();
		$qr_logo_image_path = $qr_logo_upload_dir['path'] . '/' . $qr_logo_filename;

		// Save the image file to the uploads directory
		file_put_contents( $qr_logo_image_path, $qr_logo_image_data );
		// Check if the image was downloaded successfully.
		if ( file_exists( $qr_logo_image_path ) ) {
			// Create the attachment for the uploaded image
			$qr_logo_attachment = array(
				'guid' => $qr_logo_upload_dir['url'] . '/' . $qr_logo_filename,
				'post_mime_type' => 'image/jpeg',
				'post_title' => preg_replace( '/\.[^.]+$/', '', $qr_logo_filename ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			// Insert the attachment
			$qr_logo_attachment_id = wp_insert_attachment( $qr_logo_attachment, $qr_logo_image_path );

			// Include the necessary file for media_handle_sideload().
			require_once ( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the attachment metadata
			$qr_logo_attachment_data = wp_generate_attachment_metadata( $qr_logo_attachment_id, $qr_logo_image_path );
			wp_update_attachment_metadata( $qr_logo_attachment_id, $qr_logo_attachment_data );

			$imported_file = wp_get_attachment_url( $qr_logo_attachment_id );
		}

		return $imported_file;
	}
}

/**
 * Global Admin Get Option
 */
if ( ! function_exists( 'uacf7_settings' ) ) {
	add_filter( 'uacf7_settings', 'uacf7_settings', 10, 2 );
	function uacf7_settings( $option = '' ) {

		$value = get_option( 'uacf7_settings' );


		if ( empty( $option ) ) {
			return $value;
		}

		if ( isset( $value[ $option ] ) ) {
			return $value[ $option ];
		} else {
			return false;
		}
	}
}


/*
Function: uacf7_checked
Return: checked
*/
if ( ! function_exists( 'uacf7_checked' ) ) {
	function uacf7_checked( $name ) {

		//Get settings option
		$uacf7_options = get_option( apply_filters( 'uacf7_option_name', 'uacf7_option_name' ) );

		if ( isset( $uacf7_options[ $name ] ) && $uacf7_options[ $name ] === 'on' ) {
			return 'checked';
		}
	}
}

/*
Function: uacf7_print_r
Return: checked
*/
if ( ! function_exists( 'uacf7_print_r' ) ) {
	function uacf7_print_r( ...$args ) {
		echo '<pre style="padding-left: 180px;">';
		foreach ( $args as $arg ) {
			print_r( $arg );
		}
		echo '</pre>';
		// exit;
	}
}


/*
Function: uacf7_get_form_option
Return: checked
*/
if ( ! function_exists( 'uacf7_get_form_option' ) ) {
	function uacf7_get_form_option( $id, $key ) {
		$value = get_post_meta( $id, 'uacf7_form_opt', true );

		if ( empty( $key ) ) {
			return $value;
		}
		if ( isset( $value[ $key ] ) ) {
			return $value[ $key ];
		} else {
			return false;
		}

	}
}


/*
 * Hook: uacf7_multistep_pro_features
 * Multistep pro features demo
 */
add_action( 'uacf7_multistep_pro_features', 'uacf7_multistep_pro_features_demo', 5, 2 );
function uacf7_multistep_pro_features_demo( $all_steps, $form_id ) {
	if ( ! isset( $all_steps[0] ) )
		return;
	if ( empty( array_filter( $all_steps ) ) )
		return;
	?>
	<div class="multistep_fields_row" style="display: flex; flex-direction: column;">
		<?php
		$step_count = 1;
		foreach ( $all_steps as $step ) {
			?>
			<h3><strong>Step <?php echo $step_count; ?> <a style="color:red" target="_blank"
						href="https://cf7addons.com/pricing/">(Pro)</a></strong></h3>
			<?php
			if ( $step_count == 1 ) {
				?>
				<div>
					<p><label
							for="<?php echo 'next_btn_' . $step->name; ?>"><?php echo __( 'Change next button text for this Step', 'ultimate-addons-cf7' ) ?></label>
					</p>
					<input id="<?php echo 'next_btn_' . $step->name; ?>" type="text" name="" value=""
						placeholder="<?php echo esc_html__( 'Next', 'ultimate-addons-cf7-pro' ) ?>">
				</div>
				<?php
			} else {

				if ( count( $all_steps ) == $step_count ) {
					?>
					<div>
						<p><label for="<?php echo 'prev_btn_' . $step->name; ?>">
								<?php echo __( 'Change previous button text for this Step', 'ultimate-addons-cf7' ) ?>
							</label></p>
						<input id="<?php echo 'prev_btn_' . $step->name; ?>" type="text" name="" value=""
							placeholder="<?php echo esc_html__( 'Previous', 'ultimate-addons-cf7-pro' ) ?>">
					</div>
					<?php

				} else {
					?>
					<div class="multistep_fields_row-">
						<div class="multistep_field_column">
							<p><label for="<?php echo 'prev_btn_' . $step->name; ?>">
									<?php echo __( 'Change previous button text for this Step', 'ultimate-addons-cf7' ) ?>
								</label></p>
							<input id="<?php echo 'prev_btn_' . $step->name; ?>" type="text" name="" value=""
								placeholder="<?php echo esc_html__( 'Previous', 'ultimate-addons-cf7-pro' ) ?>">
						</div>

						<div class="multistep_field_column">
							<p><label for="<?php echo 'next_btn_' . $step->name; ?>">
									<?php echo __( 'Change next button text for this Step', 'ultimate-addons-cf7' ) ?>
								</label></p>
							<input id="<?php echo 'next_btn_' . $step->name; ?>" type="text" name="" value=""
								placeholder="<?php echo esc_html__( 'Next', 'ultimate-addons-cf7-pro' ) ?>">
						</div>
					</div>
					<?php
				}

			}
			?>
			<div class="uacf7_multistep_progressbar_image_row">
				<p><label for="<?php echo esc_attr( 'uacf7_progressbar_image_' . $step->name ); ?>">
						<?php echo __( 'Add progressbar image for this step', 'ultimate-addons-cf7' ) ?>
					</label></p>
				<input class="uacf7_multistep_progressbar_image"
					id="<?php echo esc_attr( 'uacf7_progressbar_image_' . $step->name ); ?>" type="url" name="" value=""> <a
					class="button-primary" href="#">
					<?php echo __( 'Add or Upload Image', 'ultimate-addons-cf7' ) ?>
				</a>

				<div class="multistep_fields_row step-title-description col-50">
					<div class="multistep_field_column">
						<p><label for="<?php echo 'step_desc_' . $step->name; ?>">
								<?php echo __( 'Step description', 'ultimate-addons-cf7' ) ?>
							</label></p>
						<textarea id="<?php echo 'step_desc_' . $step->name; ?>" type="text" name="" cols="40" rows="3"
							placeholder="<?php echo esc_html__( 'Step description', 'ultimate-addons-cf7-pro' ) ?>"></textarea>
					</div>

					<div class="multistep_field_column">
						<p><label for="<?php echo 'desc_title_' . $step->name; ?>">
								<?php echo __( 'Description title', 'ultimate-addons-cf7' ) ?>
							</label></p>
						<input id="<?php echo 'desc_title_' . $step->name; ?>" type="text" name="" value=""
							placeholder="<?php echo esc_html__( 'Description title', 'ultimate-addons-cf7-pro' ) ?>">
					</div>
				</div>
			</div>
			<?php
			$step_count++;
		}
		?>
	</div>
	<?php
}

/*
 * Progressbar style
 */
add_action( 'uacf7_multistep_before_form', 'uacf7_multistep_progressbar_style', 10 );
function uacf7_multistep_progressbar_style( $form_id ) {
	$meta = uacf7_get_form_option( $form_id, 'multistep' );
	$uacf7_multistep_progressbar_color_option = isset( $meta['uacf7_multistep_progressbar_color_option'] ) ? $meta['uacf7_multistep_progressbar_color_option'] : '';
	$uacf7_multistep_circle_width = isset( $meta['uacf7_multistep_circle_width'] ) ? $meta['uacf7_multistep_circle_width'] : '';
	$uacf7_multistep_circle_height = isset( $meta['uacf7_multistep_circle_height'] ) ? $meta['uacf7_multistep_circle_height'] : '';
	$uacf7_multistep_circle_bg_color = isset( $uacf7_multistep_progressbar_color_option['uacf7_multistep_circle_bg_color'] ) ? $uacf7_multistep_progressbar_color_option['uacf7_multistep_circle_bg_color'] : '';
	$uacf7_multistep_circle_font_color = isset( $uacf7_multistep_progressbar_color_option['uacf7_multistep_circle_font_color'] ) ? $uacf7_multistep_progressbar_color_option['uacf7_multistep_circle_font_color'] : '';
	$uacf7_multistep_circle_border_radious = isset( $meta['uacf7_multistep_circle_border_radious'] ) ? $meta['uacf7_multistep_circle_border_radious'] : '';
	$uacf7_multistep_font_size = isset( $meta['uacf7_multistep_font_size'] ) ? $meta['uacf7_multistep_font_size'] : '';
	$uacf7_multistep_circle_active_color = isset( $uacf7_multistep_progressbar_color_option['uacf7_multistep_circle_active_color'] ) ? $uacf7_multistep_progressbar_color_option['uacf7_multistep_circle_active_color'] : '';
	$uacf7_multistep_progress_line_color = isset( $uacf7_multistep_progressbar_color_option['uacf7_multistep_progress_line_color'] ) ? $uacf7_multistep_progressbar_color_option['uacf7_multistep_progress_line_color'] : '';
	?>
	<style>
		.steps-form .steps-row .steps-step .btn-circle {
			<?php if ( ! empty( $uacf7_multistep_circle_width ) )
				echo 'width: ' . esc_attr( $uacf7_multistep_circle_width ) . 'px;'; ?>
			<?php if ( ! empty( $uacf7_multistep_circle_height ) )
				echo 'height: ' . esc_attr( $uacf7_multistep_circle_height ) . 'px;'; ?>
			<?php if ( $uacf7_multistep_circle_border_radious != '' )
				echo 'border-radius: ' . $uacf7_multistep_circle_border_radious . 'px;'; ?>
			<?php if ( ! empty( $uacf7_multistep_circle_height ) )
				echo 'line-height: ' . esc_attr( $uacf7_multistep_circle_height ) . 'px;'; ?>
			<?php if ( ! empty( $uacf7_multistep_circle_bg_color ) )
				echo 'background-color: ' . esc_attr( $uacf7_multistep_circle_bg_color ) . ' !important;'; ?>
			<?php if ( ! empty( $uacf7_multistep_circle_font_color ) )
				echo 'color: ' . esc_attr( $uacf7_multistep_circle_font_color ) . ' !important;'; ?>
			<?php if ( ! empty( $uacf7_multistep_font_size ) )
				echo 'font-size: ' . esc_attr( $uacf7_multistep_font_size ) . 'px;'; ?>
		}

		.steps-form .steps-row .steps-step .btn-circle img {
			<?php if ( $uacf7_multistep_circle_border_radious != 0 )
				echo 'border-radius: ' . $uacf7_multistep_circle_border_radious . 'px !important;'; ?>
		}

		.steps-form .steps-row .steps-step .btn-circle.uacf7-btn-active,
		.steps-form .steps-row .steps-step .btn-circle:hover,
		.steps-form .steps-row .steps-step .btn-circle:focus,
		.steps-form .steps-row .steps-step .btn-circle:active {
			<?php if ( ! empty( $uacf7_multistep_circle_active_color ) )
				echo 'background-color: ' . esc_attr( $uacf7_multistep_circle_active_color ) . ' !important;'; ?>
			<?php if ( ! empty( $uacf7_multistep_circle_font_color ) )
				echo 'color: ' . esc_attr( $uacf7_multistep_circle_font_color ) . ';'; ?>
		}

		.steps-form .steps-row .steps-step p {
			<?php if ( ! empty( $uacf7_multistep_font_size ) )
				echo 'font-size: ' . esc_attr( $uacf7_multistep_font_size ) . 'px;'; ?>
		}

		.steps-form .steps-row::before {
			<?php if ( ! empty( $uacf7_multistep_circle_height ) )
				echo 'top: ' . esc_attr( $uacf7_multistep_circle_height / 2 ) . 'px;'; ?>
		}

		<?php if ( ! empty( $uacf7_multistep_progress_line_color ) ) : ?>
			.steps-form .steps-row::before {
				background-color:
					<?php echo esc_attr( $uacf7_multistep_progress_line_color ); ?>
				;
			}

		<?php endif; ?>
	</style>
	<?php
}


//Add wrapper to contact form 7
add_filter( 'wpcf7_contact_form_properties', 'uacf7_add_wrapper_to_cf7_form', 10, 2 );
function uacf7_add_wrapper_to_cf7_form( $properties, $cfform ) {
	if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

		$auto_cart = uacf7_get_form_option( $cfform->id(), 'auto_cart' );
        $uacf7_enable_product_auto_cart = isset($auto_cart['uacf7_enable_product_auto_cart']) ? $auto_cart['uacf7_enable_product_auto_cart'] : false;

		$form_meta = uacf7_get_form_option( $cfform->id(), 'styler' );
		$form_styles = isset( $form_meta['uacf7_enable_form_styles'] ) ? $form_meta['uacf7_enable_form_styles'] : false;

		$auto_cart_class = '';
		$uacf7_formStyler_class= '';

		if ( $uacf7_enable_product_auto_cart ) {
			$auto_cart_class = 'uacf7_auto_cart_'.$cfform->id();
		}

		if ( $form_styles ) {
			$uacf7_formStyler_class= 'uacf7-uacf7style uacf7-uacf7style-' . esc_attr( $cfform->id() );
		}
		
		$form = $properties['form'];
		ob_start();
		echo '<div class="uacf7-form-' . $cfform->id() . ' '.$auto_cart_class.' '.$uacf7_formStyler_class.'">' . $form . '</div>';
		$properties['form'] = ob_get_clean();

	}
	return $properties;
}

// Themefic Plugin Set Admin Notice Status
if ( ! function_exists( 'uacf7_review_activation_status' ) ) {

	function uacf7_review_activation_status() {
		$uacf7_installation_date = get_option( 'uacf7_installation_date' );
		if ( ! isset( $_COOKIE['uacf7_installation_date'] ) && empty( $uacf7_installation_date ) && $uacf7_installation_date == 0 ) {
			setcookie( 'uacf7_installation_date', 1, time() + ( 86400 * 7 ), "/" );
		} else {
			update_option( 'uacf7_installation_date', '1' );
		}
	}
	add_action( 'admin_init', 'uacf7_review_activation_status' );
}

// Themefic Plugin Review Admin Notice
if ( ! function_exists( 'uacf7_review_notice' ) ) {

	function uacf7_review_notice() {
		$get_current_screen = get_current_screen();
		if ( $get_current_screen->base == 'dashboard' ) {
			$current_user = wp_get_current_user();
			?>
			<style>
				.themefic_review_notice ul {
					display: flex;
					justify-content: flex-start;
					gap: 20px;
					flex-wrap: wrap;
				}
				.themefic_review_notice ul li a{
					text-decoration: none;
				}
			</style>
			<div class="notice notice-info themefic_review_notice">

				<?php echo sprintf(
					__( ' <p>Hey %1$s 👋, You have been using <b>%2$s</b> for quite a while. If you feel %2$s is helping your business to grow in any way, would you please help %2$s to grow by simply leaving a 5* review on the WordPress Forum?', 'ultimate-addons-cf7' ),
					$current_user->display_name,
					'Ultra Addons for Contact Form 7'
				); ?>

				<ul>
					<li><a target="_blank"
							href="<?php echo esc_url( 'https://wordpress.org/plugins/ultimate-addons-for-contact-form-7/#reviews' ) ?>"
							class=""><span
								class="dashicons dashicons-external"></span><?php _e( ' Ok, you deserve it!', 'ultimate-addons-cf7' ) ?></a>
					</li>
					<li><a href="#" class="already_done" data-status="already"><span class="dashicons dashicons-smiley"></span>
							<?php _e( 'I already did', 'ultimate-addons-cf7' ) ?></a></li>
					<li><a href="#" class="later" data-status="later"><span class="dashicons dashicons-calendar-alt"></span>
							<?php _e( 'Maybe Later', 'ultimate-addons-cf7' ) ?></a></li>
					<li><a target="_blank"
							href="<?php echo esc_url( 'https://themefic.com/docs/ultimate-addons-for-contact-form-7/' ) ?>"
							class=""><span class="dashicons dashicons-sos"></span>
							<?php _e( 'I need help', 'ultimate-addons-cf7' ) ?></a></li>
					<li><a href="#" class="never" data-status="never"><span
								class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'ultimate-addons-cf7' ) ?>
						</a></li>
				</ul>
				<button type="button" class="notice-dismiss review_notice_dismiss" data-status="never"><span
						class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'ultimate-addons-cf7' ) ?></span></button>
			</div>

			<!--   Themefic Plugin Review Admin Notice Script -->
			<script>
				jQuery(document).ready(function ($) {
					$(document).on('click', '.already_done, .later, .never, .notice-dismiss', function (event) {
						event.preventDefault();
						var $this = $(this);
						var status = $this.attr('data-status');
						$this.closest('.themefic_review_notice').css('display', 'none')
						data = {
							action: 'uacf7_review_notice_callback',
							status: status,
						};

						$.ajax({
							url: ajaxurl,
							type: 'post',
							data: data,
							success: function (data) {
							},
							error: function (data) {
							}
						});
					});

					$(document).on('click', '.review_notice_dismiss', function (event) {
						event.preventDefault();
						var $this = $(this);
						$this.closest('.themefic_review_notice').css('display', 'none')

					});
				});

			</script>
			<?php
		}
	}
	$uacf7_review_notice_status = get_option( 'uacf7_review_notice_status' );
	$uacf7_installation_date = get_option( 'uacf7_installation_date' );
	if ( isset( $uacf7_review_notice_status ) && $uacf7_review_notice_status <= 0 && $uacf7_installation_date == 1 && ! isset( $_COOKIE['uacf7_review_notice_status'] ) && ! isset( $_COOKIE['uacf7_installation_date'] ) ) {
		add_action( 'admin_notices', 'uacf7_review_notice' );
	}

}


// Themefic Plugin Review Admin Notice Ajax Callback 
if ( ! function_exists( 'uacf7_review_notice_callback' ) ) {

	function uacf7_review_notice_callback() {
		$status = $_POST['status'];
		if ( $status == 'already' ) {
			update_option( 'uacf7_review_notice_status', '1' );
		} else if ( $status == 'never' ) {
			update_option( 'uacf7_review_notice_status', '2' );
		} else if ( $status == 'later' ) {
			$cookie_name = "uacf7_review_notice_status";
			$cookie_value = "1";
			setcookie( $cookie_name, $cookie_value, time() + ( 86400 * 7 ), "/" );
			update_option( 'uacf7_review_notice_status', '0' );
		}
		wp_die();
	}
	add_action( 'wp_ajax_uacf7_review_notice_callback', 'uacf7_review_notice_callback' );

}

// if ( ! function_exists( 'uacf7_new_updated_announcement' ) ) {
// 	function uacf7_new_updated_announcement() {
// 		$current_user = wp_get_current_user();
// 		$imgurl = UACF7_URL . 'assets/img/';
// if ( ! function_exists( 'uacf7_new_updated_announcement' ) ) {
// 	function uacf7_new_updated_announcement() {
// 		$current_user = wp_get_current_user();
// 		$imgurl = UACF7_URL . 'assets/img/';
// 		<div class="notice themefic_review_notice uacf7_new_updated_anno"> -->
// echo sprintf(
// 				__( '
//                     <a style="background-image: url(%2$s/uacf7_new_updated_anno.png)" class="uacf7_new_updated_anno_banner_url" target="_blank" href="https://themefic.com/uacf7-revamped-plugin-installation-and-options/">
// 						<div class="uacf7_new_updated_anno_info_wrap">
// 							<h3>
// 								Introducing Addons For Contact Form 7 v3.3.0!
// 							</h3>
// 							<p>Get ready for an exciting announcement! We will soon unveil the highly anticipated release of <b>Addons Contact Form 7 v3.3.0</b>. Your user experience will be enhanced, and we recommend backing up your site before updating for a smooth transition</p>
// 						</div>
// 						<button class="uacf7_new_updated_anno_button">
// 							Explore What’s New
// 							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
// 								<path d="M16.0032 9.41421L7.39663 18.0208L5.98242 16.6066L14.589 8H7.00324V6H18.0032V17H16.0032V9.41421Z" fill="#382673"/>
// 							</svg>
// 						</button>
// 					</a>
//                     ', 'ultimate-addons-cf7' ),
// 				$current_user->user_login, $imgurl,
// 				'ultimate-addons-cf7'
// 			); 

// <a class="uacf7_new_updated_anno_close uacf7_never" href="#" data-status="uacf7_never">
// 	<span class="dashicons dashicons-dismiss"></span>
// </a> 

// <script>
// 				jQuery(document).ready(function ($) {
// 					$(document).on('click', '.uacf7_never', function (event) {
// 						event.preventDefault();
// 						var $this = $(this);
// 						var status = $this.attr('data-status');
// 						$this.closest('.themefic_review_notice').css('display', 'none')
// 						data = {
// 							action: 'uacf7_review_announcement_callback',
// 							status: status,
// 						};

// 						$.ajax({
// 							url: ajaxurl,
// 							type: 'post',
// 							data: data,
// 							success: function (data) {
// 							},
// 							error: function (data) {
// 							}
// 						});
// 					});
// 				});
//</script>

// </div> 

//}

// 	if ( ! isset( $_COOKIE['uacf7_review_announcement_status'] ) ) {
// 		//add_action( 'admin_notices', 'uacf7_new_updated_announcement' );
// 	}

// }

// Themefic Plugin Review Admin Notice Ajax Callback 
// if ( ! function_exists( 'uacf7_review_announcement_callback' ) ) {

// 	function uacf7_review_announcement_callback() {
// 		$status = $_POST['status'];
// 		if ( $status == 'uacf7_never' ) {
// 			$cookie_name = "uacf7_review_announcement_status";
// 			$cookie_value = "1";
// 			setcookie( $cookie_name, $cookie_value, time() + ( 86400 * 7 ), "/" );
// 			update_option( 'uacf7_review_announcement_status', '0' );
// 		}
// 		wp_die();
// 	}
// 	add_action( 'wp_ajax_uacf7_review_announcement_callback', 'uacf7_review_announcement_callback' );

// }


// Themefic Plugin Migration Callback 
if ( ! function_exists( 'uacf7_form_option_Migration_callback' ) ) {

	function uacf7_form_option_Migration_callback() {
		$migration_status = get_option( 'uacf7_settings_migration_status' );
		if ( $migration_status != true ) {

			$args_uacf7_review = array(
				'post_type' => 'uacf7_review',
				'posts_per_page' => -1,
			);
			$query_uacf7_review = new WP_Query( $args_uacf7_review );

			if ( $query_uacf7_review->have_posts() ) {

				while ( $query_uacf7_review->have_posts() ) {
					$query_uacf7_review->the_post();
					$post_id = get_the_ID();
					$meta = get_post_meta( $post_id, 'uacf7_review_opt', true ) != '' ? get_post_meta( $post_id, 'uacf7_review_opt', true ) : array();
					$meta['review_metabox']['uacf7_review_form_id'] = get_post_meta( $post_id, 'uacf7_review_form_id', true );
					$meta['review_metabox']['uacf7_reviewer_name'] = get_post_meta( $post_id, 'uacf7_reviewer_name', true );
					$meta['review_metabox']['uacf7_reviewer_image'] = get_post_meta( $post_id, 'uacf7_reviewer_image', true );
					$meta['review_metabox']['uacf7_review_title'] = get_post_meta( $post_id, 'uacf7_review_title', true );
					$meta['review_metabox']['uacf7_review_rating'] = get_post_meta( $post_id, 'uacf7_review_rating', true );
					$meta['review_metabox']['uacf7_review_desc'] = get_post_meta( $post_id, 'uacf7_review_desc', true );
					$meta['review_metabox']['uacf7_review_extra_class'] = get_post_meta( $post_id, 'uacf7_review_extra_class', true );
					$meta['review_metabox']['uacf7_review_column'] = get_post_meta( $post_id, 'uacf7_review_column', true );
					$meta['review_metabox']['uacf7_review_text_align'] = get_post_meta( $post_id, 'uacf7_review_text_align', true );
					$meta['review_metabox']['uacf7_hide_disable_review'] = get_post_meta( $post_id, 'uacf7_hide_disable_review', true );
					$meta['review_metabox']['uacf7_show_review_form'] = get_post_meta( $post_id, 'uacf7_show_review_form', true );
					$meta['review_metabox']['uacf7_review_carousel'] = get_post_meta( $post_id, 'uacf7_review_carousel', true );

					update_post_meta( $post_id, 'uacf7_review_opt', $meta );
				}
				wp_reset_postdata();
			}

			// Meta settings_migration migration 
			$args = array(
				'post_type' => 'wpcf7_contact_form',
				'posts_per_page' => -1,
			);
			$query = new WP_Query( $args );

			$forms = array();

			if ( $query->have_posts() ) :

				while ( $query->have_posts() ) :
					$query->the_post();

					$post_id = get_the_ID();
					// $uacf7_redirect_tag_support = get_post_meta( get_the_ID(), 'uacf7_redirect_tag_support', true );
					// $meta = uacf7_get_form_option($post_id, '');  

					// Current Contact Form tags
					$form_current = WPCF7_ContactForm::get_instance( $post_id );

					$meta = get_post_meta( $post_id, 'uacf7_form_opt', true ) != '' ? get_post_meta( $post_id, 'uacf7_form_opt', true ) : array();


					//  Redirection addon Migration
					$uacf7_redirect_enable = get_post_meta( get_the_ID(), 'uacf7_redirect_enable', true ) == 'yes' ? 1 : 0;
					if ( $uacf7_redirect_enable == true ) {
						$uacf7_redirect_uacf7_redirect_to_type = get_post_meta( get_the_ID(), 'uacf7_redirect_uacf7_redirect_to_type', true );
						$uacf7_redirect_page_id = get_post_meta( get_the_ID(), 'uacf7_redirect_page_id', true );
						$uacf7_redirect_external_url = get_post_meta( get_the_ID(), 'uacf7_redirect_external_url', true );
						$uacf7_conditional_redirect_conditions = get_post_meta( get_the_ID(), 'uacf7_conditional_redirect_conditions', true );
						$uacf7_redirect_target = get_post_meta( get_the_ID(), 'uacf7_redirect_target', true ) == 'yes' ? 1 : 0;
						$uacf7_redirect_type = get_post_meta( get_the_ID(), 'uacf7_redirect_type', true ) == 'yes' ? 1 : 0;
						$uacf7_redirect_tag_support = get_post_meta( get_the_ID(), 'uacf7_redirect_tag_support', true ) == 'on' ? 1 : 0;

						$meta['redirection']['uacf7_redirect_enable'] = $uacf7_redirect_enable;
						$meta['redirection']['uacf7_redirect_to_type'] = $uacf7_redirect_uacf7_redirect_to_type;
						$meta['redirection']['page_id'] = $uacf7_redirect_page_id;
						$meta['redirection']['external_url'] = $uacf7_redirect_external_url;
						$meta['redirection']['target'] = $uacf7_redirect_target;
						$meta['redirection']['uacf7_redirect_type'] = $uacf7_redirect_type;
						$meta['redirection']['uacf7_redirect_tag_support'] = $uacf7_redirect_tag_support;
						$i = 0;
						if ( $uacf7_redirect_type == 1 ) {
							if ( ! empty( $uacf7_conditional_redirect_conditions ) ) {
								foreach ( $uacf7_conditional_redirect_conditions['uacf7_cr_tn'] as $key => $value ) {
									$meta['redirection']['conditional_redirect'][ $i ]['uacf7_cr_tn'] = $uacf7_conditional_redirect_conditions['uacf7_cr_tn'][ $i ];
									$meta['redirection']['conditional_redirect'][ $i ]['uacf7_cr_field_val'] = $uacf7_conditional_redirect_conditions['uacf7_cr_field_val'][ $i ];
									$meta['redirection']['conditional_redirect'][ $i ]['uacf7_cr_redirect_to_url'] = $uacf7_conditional_redirect_conditions['uacf7_cr_redirect_to_url'][ $i ];

									$i++;

								}
							}

						}

					}

					//  Conditional addon Migration 
					$condition = get_post_meta( get_the_ID(), 'uacf7_conditions', true );
					if ( is_array( $condition ) ) {
						$count = 0;
						foreach ( $condition as $value ) {
							$meta['conditional']['conditional_repeater'][ $count ]['uacf7_cf_group'] = $value['uacf7_cf_group'];
							$meta['conditional']['conditional_repeater'][ $count ]['uacf7_cf_hs'] = $value['uacf7_cf_hs'];
							$meta['conditional']['conditional_repeater'][ $count ]['uacf_cf_condition_for'] = $value['uacf_cf_condition_for'];

							if ( ! empty( $value['uacf7_cf_conditions'] ) && isset( $value['uacf7_cf_conditions'] ) ) {
								$i = 0;
								foreach ( $value['uacf7_cf_conditions']['uacf7_cf_tn'] as $cf_key => $cf_value ) {
									$meta['conditional']['conditional_repeater'][ $count ]['uacf7_cf_conditions'][ $i ]['uacf7_cf_tn'] = $value['uacf7_cf_conditions']['uacf7_cf_tn'][ $i ];
									$meta['conditional']['conditional_repeater'][ $count ]['uacf7_cf_conditions'][ $i ]['uacf7_cf_operator'] = $value['uacf7_cf_conditions']['uacf7_cf_operator'][ $i ];
									$meta['conditional']['conditional_repeater'][ $count ]['uacf7_cf_conditions'][ $i ]['uacf7_cf_val'] = $value['uacf7_cf_conditions']['uacf7_cf_val'][ $i ];

									$i++;
								}
							}

							$count++;
						}

					}

					// Placehoder addon Migration
					$uacf7_enable_placeholder_styles = get_post_meta( get_the_ID(), 'uacf7_enable_placeholder_styles', true ) == 'on' ? 1 : 0;
					if ( $uacf7_enable_placeholder_styles == true ) {
						$uacf7_placeholder_fontsize = get_post_meta( get_the_ID(), 'uacf7_placeholder_fontsize', true );
						$uacf7_placeholder_fontstyle = get_post_meta( get_the_ID(), 'uacf7_placeholder_fontstyle', true );
						$uacf7_placeholder_fontfamily = get_post_meta( get_the_ID(), 'uacf7_placeholder_fontfamily', true );
						$uacf7_placeholder_fontweight = get_post_meta( get_the_ID(), 'uacf7_placeholder_fontweight', true );
						$uacf7_placeholder_color = get_post_meta( get_the_ID(), 'uacf7_placeholder_color', true );
						$uacf7_placeholder_background_color = get_post_meta( get_the_ID(), 'uacf7_placeholder_background_color', true );

						$meta['placeholder']['uacf7_enable_placeholder_styles'] = $uacf7_enable_placeholder_styles;
						$meta['placeholder']['uacf7_placeholder_fontsize'] = $uacf7_placeholder_fontsize;
						$meta['placeholder']['uacf7_placeholder_fontstyle'] = $uacf7_placeholder_fontstyle;
						$meta['placeholder']['uacf7_placeholder_fontfamily'] = $uacf7_placeholder_fontfamily;
						$meta['placeholder']['uacf7_placeholder_fontweight'] = $uacf7_placeholder_fontweight;
						$meta['placeholder']['uacf7_placeholder_color_option']['uacf7_placeholder_color'] = $uacf7_placeholder_color;
						$meta['placeholder']['uacf7_placeholder_color_option']['uacf7_placeholder_background_color'] = $uacf7_placeholder_background_color;
					}

					// // styler addon Migration
					$uacf7_enable_form_styles = get_post_meta( get_the_ID(), 'uacf7_enable_form_styles', true ) == 'on' ? 1 : 0;
					if ( $uacf7_enable_form_styles == true ) {
						$uacf7_uacf7style_label_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_color', true );
						$uacf7_uacf7style_label_background_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_background_color', true );
						$uacf7_uacf7style_label_font_size = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_font_size', true );
						$uacf7_uacf7style_label_font_family = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_font_family', true );
						$uacf7_uacf7style_label_font_style = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_font_style', true );
						$uacf7_uacf7style_label_font_weight = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_font_weight', true );
						$uacf7_uacf7style_label_padding_top = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_padding_top', true );
						$uacf7_uacf7style_label_padding_right = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_padding_right', true );
						$uacf7_uacf7style_label_padding_bottom = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_padding_bottom', true );
						$uacf7_uacf7style_label_padding_left = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_padding_left', true );
						$uacf7_uacf7style_label_margin_top = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_margin_top', true );
						$uacf7_uacf7style_label_margin_right = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_margin_right', true );
						$uacf7_uacf7style_label_margin_bottom = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_margin_bottom', true );
						$uacf7_uacf7style_label_margin_left = get_post_meta( get_the_ID(), 'uacf7_uacf7style_label_margin_left', true );
						$uacf7_uacf7style_input_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_color', true );
						$uacf7_uacf7style_input_background_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_background_color', true );
						$uacf7_uacf7style_input_font_size = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_font_size', true );
						$uacf7_uacf7style_input_font_family = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_font_family', true );
						$uacf7_uacf7style_input_font_style = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_font_style', true );
						$uacf7_uacf7style_input_font_weight = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_font_weight', true );
						$uacf7_uacf7style_input_height = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_height', true );
						$uacf7_uacf7style_input_border_width = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_border_width', true );

						$uacf7_uacf7style_input_border_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_border_color', true );

						$uacf7_uacf7style_input_border_style = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_border_style', true );
						$uacf7_uacf7style_input_border_radius = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_border_radius', true );
						$uacf7_uacf7style_textarea_input_height = get_post_meta( get_the_ID(), 'uacf7_uacf7style_textarea_input_height', true );
						$uacf7_uacf7style_input_padding_top = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_padding_top', true );
						$uacf7_uacf7style_input_padding_right = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_padding_right', true );
						$uacf7_uacf7style_input_padding_bottom = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_padding_bottom', true );
						$uacf7_uacf7style_input_padding_left = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_padding_left', true );
						$uacf7_uacf7style_input_margin_top = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_margin_top', true );
						$uacf7_uacf7style_input_margin_right = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_margin_right', true );
						$uacf7_uacf7style_input_margin_bottom = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_margin_bottom', true );
						$uacf7_uacf7style_input_margin_left = get_post_meta( get_the_ID(), 'uacf7_uacf7style_input_margin_left', true );
						$uacf7_uacf7style_btn_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_color', true );
						$uacf7_uacf7style_btn_background_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_background_color', true );
						$uacf7_uacf7style_btn_color_hover = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_color_hover', true );
						$uacf7_uacf7style_btn_background_color_hover = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_background_color_hover', true );
						$uacf7_uacf7style_btn_font_size = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_font_size', true );
						$uacf7_uacf7style_btn_font_style = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_font_style', true );
						$uacf7_uacf7style_btn_font_weight = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_font_weight', true );
						$uacf7_uacf7style_btn_border_width = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_border_width', true );
						$uacf7_uacf7style_btn_border_color = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_border_color', true );
						$uacf7_uacf7style_btn_border_style = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_border_style', true );
						$uacf7_uacf7style_btn_border_radius = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_border_radius', true );
						$uacf7_uacf7style_btn_width = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_width', true );
						$uacf7_uacf7style_btn_border_color_hover = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_border_color_hover', true );
						$uacf7_uacf7style_btn_padding_top = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_padding_top', true );
						$uacf7_uacf7style_btn_padding_right = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_padding_right', true );
						$uacf7_uacf7style_btn_padding_bottom = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_padding_bottom', true );
						$uacf7_uacf7style_btn_padding_left = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_padding_left', true );
						$uacf7_uacf7style_btn_margin_top = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_margin_top', true );
						$uacf7_uacf7style_btn_margin_right = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_margin_right', true );
						$uacf7_uacf7style_btn_margin_bottom = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_margin_bottom', true );
						$uacf7_uacf7style_btn_margin_left = get_post_meta( get_the_ID(), 'uacf7_uacf7style_btn_margin_left', true );
						$uacf7_uacf7style_ua_custom_css = get_post_meta( get_the_ID(), 'uacf7_uacf7style_ua_custom_css', true );


						//  Migration 
						$meta['styler']['uacf7_enable_form_styles'] = $uacf7_enable_form_styles;
						$meta['styler']['uacf7_uacf7style_label_color_option']['uacf7_uacf7style_label_color'] = $uacf7_uacf7style_label_color;
						$meta['styler']['uacf7_uacf7style_label_color_option']['uacf7_uacf7style_label_background_color'] = $uacf7_uacf7style_label_background_color;
						$meta['styler']['uacf7_uacf7style_label_font_style'] = $uacf7_uacf7style_label_font_style;
						$meta['styler']['uacf7_uacf7style_label_font_weight'] = $uacf7_uacf7style_label_font_weight;
						$meta['styler']['uacf7_uacf7style_label_font_size'] = $uacf7_uacf7style_label_font_size;
						$meta['styler']['uacf7_uacf7style_label_font_family'] = $uacf7_uacf7style_label_font_family;
						$meta['styler']['uacf7_uacf7style_label_padding_top'] = $uacf7_uacf7style_label_padding_top;
						$meta['styler']['uacf7_uacf7style_label_padding_right'] = $uacf7_uacf7style_label_padding_right;
						$meta['styler']['uacf7_uacf7style_label_padding_bottom'] = $uacf7_uacf7style_label_padding_bottom;
						$meta['styler']['uacf7_uacf7style_label_padding_left'] = $uacf7_uacf7style_label_padding_left;
						$meta['styler']['uacf7_uacf7style_label_margin_top'] = $uacf7_uacf7style_label_margin_top;
						$meta['styler']['uacf7_uacf7style_label_margin_right'] = $uacf7_uacf7style_label_margin_right;
						$meta['styler']['uacf7_uacf7style_label_margin_bottom'] = $uacf7_uacf7style_label_margin_bottom;
						$meta['styler']['uacf7_uacf7style_label_margin_left'] = $uacf7_uacf7style_label_margin_left;


						$meta['styler']['uacf7_uacf7style_input_color_option']['uacf7_uacf7style_input_color'] = $uacf7_uacf7style_input_color;
						$meta['styler']['uacf7_uacf7style_input_color_option']['uacf7_uacf7style_input_background_color'] = $uacf7_uacf7style_input_background_color;
						$meta['styler']['uacf7_uacf7style_input_font_style'] = $uacf7_uacf7style_input_font_style;
						$meta['styler']['uacf7_uacf7style_input_font_weight'] = $uacf7_uacf7style_input_font_weight;
						$meta['styler']['uacf7_uacf7style_input_font_size'] = $uacf7_uacf7style_input_font_size;
						$meta['styler']['uacf7_uacf7style_input_font_family'] = $uacf7_uacf7style_input_font_family;
						$meta['styler']['uacf7_uacf7style_input_height'] = $uacf7_uacf7style_input_height;
						$meta['styler']['uacf7_uacf7style_textarea_input_height'] = $uacf7_uacf7style_textarea_input_height;
						$meta['styler']['uacf7_uacf7style_input_padding_top'] = $uacf7_uacf7style_input_padding_top;
						$meta['styler']['uacf7_uacf7style_input_padding_right'] = $uacf7_uacf7style_input_padding_right;
						$meta['styler']['uacf7_uacf7style_input_padding_bottom'] = $uacf7_uacf7style_input_padding_bottom;
						$meta['styler']['uacf7_uacf7style_input_padding_left'] = $uacf7_uacf7style_input_padding_left;
						$meta['styler']['uacf7_uacf7style_input_margin_top'] = $uacf7_uacf7style_input_margin_top;
						$meta['styler']['uacf7_uacf7style_input_margin_right'] = $uacf7_uacf7style_input_margin_right;
						$meta['styler']['uacf7_uacf7style_input_margin_bottom'] = $uacf7_uacf7style_input_margin_bottom;
						$meta['styler']['uacf7_uacf7style_input_margin_left'] = $uacf7_uacf7style_input_margin_left;
						$meta['styler']['uacf7_uacf7style_input_border_width'] = $uacf7_uacf7style_input_border_width;
						$meta['styler']['uacf7_uacf7style_input_border_style'] = $uacf7_uacf7style_input_border_style;
						$meta['styler']['uacf7_uacf7style_input_border_radius'] = $uacf7_uacf7style_input_border_radius;
						$meta['styler']['uacf7_uacf7style_input_border_color'] = $uacf7_uacf7style_input_border_color;
						$meta['styler']['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_color'] = $uacf7_uacf7style_btn_color;
						$meta['styler']['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_background_color'] = $uacf7_uacf7style_btn_background_color;
						$meta['styler']['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_color_hover'] = $uacf7_uacf7style_btn_color_hover;
						$meta['styler']['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_background_color_hover'] = $uacf7_uacf7style_btn_background_color_hover;

						$meta['styler']['uacf7_uacf7style_btn_font_size'] = $uacf7_uacf7style_btn_font_size;
						$meta['styler']['uacf7_uacf7style_btn_font_style'] = $uacf7_uacf7style_btn_font_style;
						$meta['styler']['uacf7_uacf7style_btn_font_weight'] = $uacf7_uacf7style_btn_font_weight;
						$meta['styler']['uacf7_uacf7style_btn_width'] = $uacf7_uacf7style_btn_width;
						$meta['styler']['uacf7_uacf7style_btn_border_style'] = $uacf7_uacf7style_btn_border_style;
						$meta['styler']['uacf7_uacf7style_btn_border_color'] = $uacf7_uacf7style_btn_border_color;
						$meta['styler']['uacf7_uacf7style_btn_border_color_hover'] = $uacf7_uacf7style_btn_border_color_hover;
						$meta['styler']['uacf7_uacf7style_btn_border_width'] = $uacf7_uacf7style_btn_border_width;
						$meta['styler']['uacf7_uacf7style_btn_border_radius'] = $uacf7_uacf7style_btn_border_radius;
						$meta['styler']['uacf7_uacf7style_btn_padding_top'] = $uacf7_uacf7style_btn_padding_top;
						$meta['styler']['uacf7_uacf7style_btn_padding_right'] = $uacf7_uacf7style_btn_padding_right;
						$meta['styler']['uacf7_uacf7style_btn_padding_bottom'] = $uacf7_uacf7style_btn_padding_bottom;
						$meta['styler']['uacf7_uacf7style_btn_padding_left'] = $uacf7_uacf7style_btn_padding_left;
						$meta['styler']['uacf7_uacf7style_btn_margin_top'] = $uacf7_uacf7style_btn_margin_top;
						$meta['styler']['uacf7_uacf7style_btn_margin_right'] = $uacf7_uacf7style_btn_margin_right;
						$meta['styler']['uacf7_uacf7style_btn_margin_bottom'] = $uacf7_uacf7style_btn_margin_bottom;
						$meta['styler']['uacf7_uacf7style_btn_margin_left'] = $uacf7_uacf7style_btn_margin_left;
						$meta['styler']['uacf7_uacf7style_ua_custom_css'] = $uacf7_uacf7style_ua_custom_css;
					}

					// Multistep addon Migration
					$uacf7_multistep_is_multistep = get_post_meta( $post_id, 'uacf7_multistep_is_multistep', true ) == 'on' ? 1 : 0;
					if ( $uacf7_multistep_is_multistep == true ) {
						$meta['multistep']['uacf7_multistep_is_multistep'] = $uacf7_multistep_is_multistep;
						$meta['multistep']['uacf7_enable_multistep_progressbar'] = get_post_meta( $post_id, 'uacf7_enable_multistep_progressbar', true ) == 'on' ? 1 : 0;
						$meta['multistep']['uacf7_enable_multistep_scroll'] = get_post_meta( $post_id, 'uacf7_enable_multistep_scroll', true ) == 'on' ? 1 : 0;
						$meta['multistep']['uacf7_progressbar_style'] = get_post_meta( $post_id, 'uacf7_progressbar_style', true );
						$meta['multistep']['uacf7_multistep_use_step_labels'] = get_post_meta( $post_id, 'uacf7_multistep_use_step_labels', true ) == 'on' ? 1 : 0;
						$meta['multistep']['uacf7_multistep_circle_width'] = get_post_meta( $post_id, 'uacf7_multistep_circle_width', true );
						$meta['multistep']['uacf7_multistep_circle_height'] = get_post_meta( $post_id, 'uacf7_multistep_circle_height', true );
						$meta['multistep']['uacf7_multistep_circle_bg_color'] = get_post_meta( $post_id, 'uacf7_multistep_circle_bg_color', true );
						$meta['multistep']['uacf7_multistep_circle_font_color'] = get_post_meta( $post_id, 'uacf7_multistep_circle_font_color', true );
						$meta['multistep']['uacf7_multistep_circle_border_radious'] = get_post_meta( $post_id, 'uacf7_multistep_circle_border_radious', true );
						$meta['multistep']['uacf7_multistep_font_size'] = get_post_meta( $post_id, 'uacf7_multistep_font_size', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_circle_bg_color'] = get_post_meta( $post_id, 'uacf7_multistep_circle_bg_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_circle_active_color'] = get_post_meta( $post_id, 'uacf7_multistep_circle_active_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_circle_font_color'] = get_post_meta( $post_id, 'uacf7_multistep_circle_font_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_progress_bg_color'] = get_post_meta( $post_id, 'uacf7_multistep_progress_bg_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_progress_line_color'] = get_post_meta( $post_id, 'uacf7_multistep_progress_line_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_step_title_color'] = get_post_meta( $post_id, 'uacf7_multistep_step_title_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_progressbar_title_color'] = get_post_meta( $post_id, 'uacf7_multistep_progressbar_title_color', true );
						$meta['multistep']['uacf7_multistep_progressbar_color_option']['uacf7_multistep_step_description_color'] = get_post_meta( $post_id, 'uacf7_multistep_step_description_color', true );
						$meta['multistep']['uacf7_multistep_step_height'] = get_post_meta( $post_id, 'uacf7_multistep_step_height', true );
						$meta['multistep']['uacf7_multistep_button_padding_tb'] = get_post_meta( $post_id, 'uacf7_multistep_button_padding_tb', true );
						$meta['multistep']['uacf7_multistep_button_padding_lr'] = get_post_meta( $post_id, 'uacf7_multistep_button_padding_lr', true );
						$meta['multistep']['uacf7_multistep_next_prev_option']['uacf7_multistep_button_bg'] = get_post_meta( $post_id, 'uacf7_multistep_button_bg', true );
						$meta['multistep']['uacf7_multistep_next_prev_option']['uacf7_multistep_button_color'] = get_post_meta( $post_id, 'uacf7_multistep_button_color', true );
						$meta['multistep']['uacf7_multistep_next_prev_option']['uacf7_multistep_button_border_color'] = get_post_meta( $post_id, 'uacf7_multistep_button_border_color', true );
						$meta['multistep']['uacf7_multistep_next_prev_option']['uacf7_multistep_button_hover_bg'] = get_post_meta( $post_id, 'uacf7_multistep_button_hover_bg', true );
						$meta['multistep']['uacf7_multistep_next_prev_option']['uacf7_multistep_button_hover_color'] = get_post_meta( $post_id, 'uacf7_multistep_button_hover_color', true );
						$meta['multistep']['uacf7_multistep_next_prev_option']['uacf7_multistep_button_border_hover_color'] = get_post_meta( $post_id, 'uacf7_multistep_button_border_hover_color', true );
						$meta['multistep']['uacf7_multistep_button_border_radius'] = get_post_meta( $post_id, 'uacf7_multistep_button_border_radius', true );



						$all_steps = $form_current->scan_form_tags( array( 'type' => 'uacf7_step_start' ) );

						$step_count = 1;
						foreach ( $all_steps as $step ) {

							if ( $step_count == 1 ) {
								$meta['multistep'][ 'next_btn_' . $step->name . '' ] = get_post_meta( $post_id, 'next_btn_' . $step->name . '', true );
							} else {
								if ( count( $all_steps ) == $step_count ) {
									$meta['multistep'][ 'prev_btn_' . $step->name . '' ] = get_post_meta( $post_id, 'prev_btn_' . $step->name . '', true );
								} else {
									$meta['multistep'][ 'next_btn_' . $step->name . '' ] = get_post_meta( $post_id, 'next_btn_' . $step->name . '', true );
									$meta['multistep'][ 'prev_btn_' . $step->name . '' ] = get_post_meta( $post_id, 'prev_btn_' . $step->name . '', true );
								}
							}

							$meta['multistep'][ 'uacf7_progressbar_image_' . $step->name . '' ] = get_option( 'uacf7_progressbar_image_' . $step->name . '', true );
							$meta['multistep'][ 'desc_title_' . $step->name . '' ] = get_post_meta( $post_id, 'desc_title_' . $step->name . '', true );
							$meta['multistep'][ 'step_desc_' . $step->name . '' ] = get_post_meta( $post_id, 'step_desc_' . $step->name . '', true );

							$step_count++;
						}

					}

					// Booking addon Migration
					$bf_enable = get_post_meta( $post_id, 'bf_enable', true ) == 'on' ? 1 : get_post_meta( $post_id, 'bf_enable', true );
					$booking = isset( $meta['booking'] ) ? $meta['booking'] : array();

					if ( $bf_enable == true ) {
						$booking['bf_enable'] = $bf_enable;
						$booking['bf_duplicate_status'] = get_post_meta( $post_id, 'bf_duplicate_status', true ) == '1' ? 1 : 0;
						$booking['calendar_event_enable'] = get_post_meta( $post_id, 'calendar_event_enable', true ) == 'on' ? 1 : 0;
						$booking['event_email'] = get_post_meta( $post_id, 'event_email', true );
						$booking['event_summary'] = get_post_meta( $post_id, 'event_summary', true );
						$booking['event_date'] = get_post_meta( $post_id, 'event_date', true );
						$booking['event_time'] = get_post_meta( $post_id, 'event_time', true );
						$booking['date_mode_front'] = get_post_meta( $post_id, 'date_mode_front', true );
						$booking['bf_date_theme'] = get_post_meta( $post_id, 'bf_date_theme', true );
						$booking['bf_allowed_date'] = get_post_meta( $post_id, 'bf_allowed_date', true );
						$booking['allowed_min_max_date']['from'] = get_post_meta( $post_id, 'min_date', true );
						$booking['allowed_min_max_date']['to'] = get_post_meta( $post_id, 'max_date', true );
						$booking['allowed_specific_date'] = get_post_meta( $post_id, 'allowed_specific_date', true );
						$booking['disable_day'][0] = get_post_meta( $post_id, 'disable_day_1', true );
						$booking['disable_day'][1] = get_post_meta( $post_id, 'disable_day_2', true );
						$booking['disable_day'][2] = get_post_meta( $post_id, 'disable_day_3', true );
						$booking['disable_day'][3] = get_post_meta( $post_id, 'disable_day_4', true );
						$booking['disable_day'][4] = get_post_meta( $post_id, 'disable_day_5', true );
						$booking['disable_day'][5] = get_post_meta( $post_id, 'disable_day_6', true );
						$booking['disable_day'][6] = get_post_meta( $post_id, 'disable_day_0', true );
						$booking['disabled_date']['from'] = get_post_meta( $post_id, 'disabled_start_date', true );
						$booking['disabled_date']['to'] = get_post_meta( $post_id, 'disabled_end_date', true );
						$booking['disabled_specific_date'] = get_post_meta( $post_id, 'disabled_specific_date', true );
						$booking['time_format_front'] = get_post_meta( $post_id, 'time_format_front', true );
						$booking['min_time'] = get_post_meta( $post_id, 'min_time', true );
						$booking['max_time'] = get_post_meta( $post_id, 'max_time', true );
						$booking['from_dis_time'] = get_post_meta( $post_id, 'from_dis_time', true );
						$booking['to_dis_time'] = get_post_meta( $post_id, 'to_dis_time', true );
						$booking['uacf7_time_interval'] = get_post_meta( $post_id, 'uacf7_time_interval', true );
						$booking['time_one_step'] = get_post_meta( $post_id, 'time_one_step', true );
						$booking['time_two_step'] = get_post_meta( $post_id, 'time_two_step', true );
						$booking['bf_allowed_time'] = get_post_meta( $post_id, 'bf_allowed_time', true );
						$booking['allowed_time_day'][0] = get_post_meta( $post_id, 'time_day_1', true );
						$booking['allowed_time_day'][1] = get_post_meta( $post_id, 'time_day_2', true );
						$booking['allowed_time_day'][2] = get_post_meta( $post_id, 'time_day_3', true );
						$booking['allowed_time_day'][3] = get_post_meta( $post_id, 'time_day_4', true );
						$booking['allowed_time_day'][4] = get_post_meta( $post_id, 'time_day_5', true );
						$booking['allowed_time_day'][5] = get_post_meta( $post_id, 'time_day_6', true );
						$booking['allowed_time_day'][6] = get_post_meta( $post_id, 'time_day_0', true );
						$booking['specific_date_time'] = get_post_meta( $post_id, 'specific_date_time', true );
						$booking['min_day_time'] = get_post_meta( $post_id, 'min_day_time', true );
						$booking['max_day_time'] = get_post_meta( $post_id, 'max_day_time', true );
						$booking['bf_woo'] = get_post_meta( $post_id, 'bf_woo', true );
						$booking['bf_product'] = get_post_meta( $post_id, 'bf_product', true );
						$booking['bf_product_id'] = get_post_meta( $post_id, 'bf_product_id', true );
						$booking['bf_product_name'] = get_post_meta( $post_id, 'bf_product_name', true );
						$booking['bf_product_price'] = get_post_meta( $post_id, 'bf_product_price', true );
						$meta['booking'] = $booking;
					}

					// Post Submission addon Migration
					$enable_post_submission = get_post_meta( $post_id, 'enable_post_submission', true ) == 'yes' ? 1 : 0;
					$post_submission = isset( $meta['post_submission'] ) ? $meta['post_submission'] : array();
					if ( $enable_post_submission == true ) {
						$post_submission['enable_post_submission'] = $enable_post_submission;
						$post_submission['post_submission_post_type'] = get_post_meta( $post_id, 'post_submission_post_type', true );
						$post_submission['post_submission_post_status'] = get_post_meta( $post_id, 'post_submission_post_status', true );
						$meta['post_submission'] = $post_submission;
					}

					// Mailchimp addon Migration
					$uacf7_mailchimp_form_enable = get_post_meta( $post_id, 'uacf7_mailchimp_form_enable', true ) == 'enable' ? 1 : 0;
					$mailchimp = isset( $meta['mailchimp'] ) ? $meta['mailchimp'] : array();
					if ( $uacf7_mailchimp_form_enable == true ) {
						$mailchimp['uacf7_mailchimp_form_enable'] = $uacf7_mailchimp_form_enable;
						$mailchimp['uacf7_mailchimp_form_type'] = get_post_meta( $post_id, 'uacf7_mailchimp_form_type', true );
						$mailchimp['uacf7_mailchimp_audience'] = get_post_meta( $post_id, 'uacf7_mailchimp_audience', true );
						$mailchimp['uacf7_mailchimp_subscriber_email'] = get_post_meta( $post_id, 'uacf7_mailchimp_subscriber_email', true );
						$mailchimp['uacf7_mailchimp_subscriber_fname'] = get_post_meta( $post_id, 'uacf7_mailchimp_subscriber_fname', true );
						$mailchimp['uacf7_mailchimp_subscriber_lname'] = get_post_meta( $post_id, 'uacf7_mailchimp_subscriber_lname', true );
						$mailchimp['uacf7_mailchimp_merge_fields'] = get_post_meta( $post_id, 'uacf7_mailchimp_merge_fields', true );
						$meta['mailchimp'] = $mailchimp;

					}


					// PDF Generator Enable
					$pdf = isset( $meta['pdf_generator'] ) ? $meta['pdf_generator'] : array();
					$uacf7_enable_pdf_generator = get_post_meta( $post_id, 'uacf7_enable_pdf_generator', true ) == 'on' ? 1 : get_post_meta( $post_id, 'uacf7_enable_pdf_generator', true );

					if ( $uacf7_enable_pdf_generator == true ) {
						$pdf['uacf7_enable_pdf_generator'] = $uacf7_enable_pdf_generator;
						$pdf['uacf7_pdf_name'] = get_post_meta( $post_id, 'uacf7_pdf_name', true );
						$pdf['pdf_send_to'] = get_post_meta( $post_id, 'pdf_send_to', true );
						$pdf['uacf7_pdf_disable_header_footer'][0] = get_post_meta( $post_id, 'uacf7_pdf_disable_header', true ) == true ? 'header' : 0;
						$pdf['uacf7_pdf_disable_header_footer'][1] = get_post_meta( $post_id, 'uacf7_pdf_disable_footer', true ) == true ? 'footer' : 0;
						$pdf['pdf_bg_upload_image'] = get_post_meta( $post_id, 'pdf_bg_upload_image', true );
						$pdf['pdf_content_bg_color'] = get_post_meta( $post_id, 'pdf_content_bg_color', true );
						$pdf['customize_pdf'] = get_post_meta( $post_id, 'customize_pdf', true );
						$pdf['pdf_header_upload_image'] = get_post_meta( $post_id, 'pdf_header_upload_image', true );
						$pdf['pdf_header_color'] = get_post_meta( $post_id, 'pdf_header_color', true );
						$pdf['pdf_header_bg_color'] = get_post_meta( $post_id, 'pdf_header_bg_color', true );
						$pdf['customize_pdf_header'] = get_post_meta( $post_id, 'customize_pdf_header', true );
						$pdf['pdf_footer_color'] = get_post_meta( $post_id, 'pdf_footer_color', true );
						$pdf['pdf_footer_bg_color'] = get_post_meta( $post_id, 'pdf_footer_bg_color', true );
						$pdf['customize_pdf_footer'] = get_post_meta( $post_id, 'customize_pdf_footer', true );
						$pdf['custom_pdf_css'] = get_post_meta( $post_id, 'custom_pdf_css', true );
						$meta['pdf_generator'] = $pdf;
					}

					// Conversation form addon Migration
					$conversational = isset( $meta['conversational_form'] ) ? $meta['conversational_form'] : array();
					$uacf7_conversation_form_enable = get_post_meta( $post_id, 'uacf7_is_conversational', true ) == 'on' ? 1 : 0;

					if ( $uacf7_conversation_form_enable == true ) {
						$conversational['uacf7_is_conversational'] = $uacf7_conversation_form_enable;
						$conversational['uacf7_full_screen'] = get_post_meta( $post_id, 'uacf7_full_screen', true ) == 'on' ? 1 : 0;
						$conversational['uacf7_enable_progress_bar'] = get_post_meta( $post_id, 'uacf7_enable_progress_bar', true ) == 'on' ? 1 : 0;
						$conversational['uacf7_conversational_intro'] = get_post_meta( $post_id, 'uacf7_conversational_intro', true ) == 'on' ? 1 : 0;
						$conversational['uacf7_conversational_thankyou'] = get_post_meta( $post_id, 'uacf7_conversational_thankyou', true ) == 'on' ? 1 : 0;
						$conversational['uacf7_conversational_style'] = get_post_meta( $post_id, 'uacf7_conversational_style', true );
						$conversational['uacf7_conversational_bg_color'] = get_post_meta( $post_id, 'uacf7_conversational_bg_color', true );
						$conversational['uacf7_conversational_button_color'] = get_post_meta( $post_id, 'uacf7_conversational_button_color', true );
						$conversational['uacf7_conversational_button_bg_color'] = get_post_meta( $post_id, 'uacf7_conversational_button_bg_color', true );
						$conversational['uacf7_conversational_bg_image'] = get_post_meta( $post_id, 'uacf7_conversational_bg_image', true );
						$conversational['uacf7_progress_bar_height'] = get_post_meta( $post_id, 'uacf7_progress_bar_height', true );
						$conversational['uacf7_progress_bar_bg_color'] = get_post_meta( $post_id, 'uacf7_progress_bar_bg_color', true );
						$conversational['uacf7_progress_bar_completed_bg_color'] = get_post_meta( $post_id, 'uacf7_progress_bar_completed_bg_color', true );
						$conversational['uacf7_conversational_intro_title'] = get_post_meta( $post_id, 'uacf7_conversational_intro_title', true );
						$conversational['uacf7_conversational_intro_button'] = get_post_meta( $post_id, 'uacf7_conversational_intro_button', true );
						$conversational['uacf7_conversational_intro_bg_color'] = get_post_meta( $post_id, 'uacf7_conversational_intro_bg_color', true );
						$conversational['uacf7_conversational_intro_text_color'] = get_post_meta( $post_id, 'uacf7_conversational_intro_text_color', true );
						$conversational['uacf7_conversational_intro_image'] = get_post_meta( $post_id, 'uacf7_conversational_intro_image', true );
						$conversational['uacf7_conversational_intro_message'] = get_post_meta( $post_id, 'uacf7_conversational_intro_message', true );
						$conversational['uacf7_conversational_thank_you_title'] = get_post_meta( $post_id, 'uacf7_conversational_thank_you_title', true );
						$conversational['uacf7_conversational_thank_you_button'] = get_post_meta( $post_id, 'uacf7_conversational_thank_you_button', true );
						$conversational['uacf7_conversational_thank_you_url'] = get_post_meta( $post_id, 'uacf7_conversational_thank_you_url', true );
						$conversational['uacf7_conversational_thankyou_bg_color'] = get_post_meta( $post_id, 'uacf7_conversational_thankyou_bg_color', true );
						$conversational['uacf7_conversational_thankyou_text_color'] = get_post_meta( $post_id, 'uacf7_conversational_thankyou_text_color', true );
						$conversational['uacf7_conversational_thankyou_image'] = get_post_meta( $post_id, 'uacf7_conversational_thankyou_image', true );
						$conversational['uacf7_conversational_thank_you_message'] = get_post_meta( $post_id, 'uacf7_conversational_thank_you_message', true );
						$conversational['custom_conv_css'] = wp_kses_post( get_post_meta( $post_id, 'custom_conv_css', true ) );

						$uacf7_conversational_field = get_post_meta( $post_id, 'uacf7_conversational_field', true );

						$count = 0;
						if ( ! empty( $uacf7_conversational_field ) ) {
							foreach ( $uacf7_conversational_field as $field_key => $field_value ) {
								$conversational['uacf7_conversational_steps'][ $count ] = $field_value;
								$conversational['uacf7_conversational_steps'][ $count ]['steps_name'] = $field_key;
								$count++;
							}
						}


						$meta['conversational_form'] = $conversational;
					}

					// Submission ID addon Migration
					$submission = isset( $meta['submission_id'] ) ? $meta['submission_id'] : array();

					$uacf7_submission_id_enable = get_post_meta( $post_id, 'uacf7_submission_id_enable', true ) == 'on' ? 1 : 0;
					if ( $uacf7_submission_id_enable == 1 ) {
						$uacf7_submission_id = get_post_meta( $post_id, 'uacf7_submission_id', true );
						$uacf7_submission_id_step = get_post_meta( $post_id, 'uacf7_submission_id_step', true );
						$submission['uacf7_submission_id_enable'] = 1;
						$submission['uacf7_submission_id'] = $uacf7_submission_id;
						$submission['uacf7_submission_id_step'] = $uacf7_submission_id_step;
						$meta['submission_id'] = $submission;
					}

					//Telegram Addon Migration 
					$telegram = isset( $meta['telegram'] ) ? $meta['telegram'] : array();
					$uacf7_telegram_settings = get_post_meta( $post_id, 'uacf7_telegram_settings', true );
					$uacf7_telegram_enable = is_array( $uacf7_telegram_settings ) && isset( $uacf7_telegram_settings['uacf7_telegram_enable'] ) ? $uacf7_telegram_settings['uacf7_telegram_enable'] : '';

					if ( $uacf7_telegram_enable == 'on' ) {
						$uacf7_telegram_bot_token = isset( $uacf7_telegram_settings['uacf7_telegram_bot_token'] ) ? $uacf7_telegram_settings['uacf7_telegram_bot_token'] : '';
						$uacf7_telegram_chat_id = isset( $uacf7_telegram_settings['uacf7_telegram_chat_id'] ) ? $uacf7_telegram_settings['uacf7_telegram_chat_id'] : '';
						$telegram['uacf7_telegram_enable'] = 1;
						$telegram['uacf7_telegram_bot_token'] = $uacf7_telegram_bot_token;
						$telegram['uacf7_telegram_chat_id'] = $uacf7_telegram_chat_id;
						$meta['telegram'] = $telegram;

					}

					//Signature Addon 
					$signature = isset( $meta['signature'] ) ? $meta['signature'] : array();
					$uacf7_signature_settings = get_post_meta( $post_id, 'uacf7_signature_settings', true );
					$uacf7_signature_enable = is_array( $uacf7_signature_settings ) && isset( $uacf7_signature_settings['uacf7_signature_enable'] ) ? $uacf7_signature_settings['uacf7_signature_enable'] : '';


					if ( $uacf7_signature_enable == 'on' ) {
						$uacf7_signature_bg_color = isset( $uacf7_signature_settings['uacf7_signature_bg_color'] ) ? $uacf7_signature_settings['uacf7_signature_bg_color'] : '';
						$uacf7_signature_pen_color = isset( $uacf7_signature_settings['uacf7_signature_pen_color'] ) ? $uacf7_signature_settings['uacf7_signature_pen_color'] : '';
						$signature['uacf7_signature_enable'] = 1;
						$signature['uacf7_signature_bg_color'] = $uacf7_signature_bg_color;
						$signature['uacf7_signature_pen_color'] = $uacf7_signature_pen_color;
						$signature['uacf7_signature_pad_width'] = isset( $uacf7_signature_settings['uacf7_signature_pad_width'] ) ? $uacf7_signature_settings['uacf7_signature_pad_width'] : '300';
						$signature['uacf7_signature_pad_height'] = isset( $uacf7_signature_settings['uacf7_signature_pad_height'] ) ? $uacf7_signature_settings['uacf7_signature_pad_height'] : '100';
						$meta['signature'] = $signature;

					}


					// Pre Populate addon Migration
					$pre_populated = isset( $meta['pre_populated'] ) ? $meta['pre_populated'] : array();

					$pre_populate_enable = get_post_meta( $post_id, 'pre_populate_enable', true ) == 1 ? 1 : 0;

					if ( $pre_populate_enable == 1 ) {
						$pre_populated['pre_populate_enable'] = $pre_populate_enable;
						$pre_populated['data_redirect_url'] = get_post_meta( $post_id, 'data_redirect_url', true );
						$pre_populated['pre_populate_form'] = get_post_meta( $post_id, 'pre_populate_form', true );

						$pre_populate_passing_field = get_post_meta( $post_id, 'pre_populate_passing_field', true );
						$count = 0;
						if ( is_array( $pre_populate_passing_field ) ) {
							foreach ( $pre_populate_passing_field as $field_key => $field_value ) {
								// $pre_populated['pre_populate_passing_field'][$count] = $field_value;  
								$pre_populated['pre_populate_passing_field'][ $count ]['field_name'] = $field_value;
								$count++;
							}
						}

						$meta['pre_populated'] = $pre_populated;

					}

					// Range Slider Filter addon Migration
					$range_slider = isset( $meta['range_slider'] ) ? $meta['range_slider'] : array();
					$range_slider['uacf7_range_selection_color'] = get_post_meta( $post_id, 'uacf7_range_selection_color', true );
					$range_slider['uacf7_range_handle_color'] = get_post_meta( $post_id, 'uacf7_range_handle_color', true );
					$range_slider['uacf7_range_handle_width'] = get_post_meta( $post_id, 'uacf7_range_handle_width', true );
					$range_slider['uacf7_range_handle_height'] = get_post_meta( $post_id, 'uacf7_range_handle_height', true );
					$range_slider['uacf7_range_handle_border_radius'] = get_post_meta( $post_id, 'uacf7_range_handle_border_radius', true );
					$range_slider['uacf7_range_slider_height'] = get_post_meta( $post_id, 'uacf7_range_slider_height', true );
					$meta['range_slider'] = $range_slider;

					// Auto Cart Checkout addon Migration
					$auto_cart = isset( $meta['auto_cart'] ) ? $meta['auto_cart'] : array();
					$uacf7_enable_product_auto_cart = get_post_meta( $post_id, 'uacf7_enable_product_auto_cart', true ) == 'on' ? 1 : get_post_meta( $post_id, 'uacf7_enable_product_auto_cart', true );

					if ( $uacf7_enable_product_auto_cart == true ) {
						$auto_cart['uacf7_enable_product_auto_cart'] = $uacf7_enable_product_auto_cart;
						$auto_cart['uacf7_product_auto_cart_redirect_to'] = get_post_meta( $post_id, 'uacf7_product_auto_cart_redirect_to', true );
						$auto_cart['uacf7_enable_track_order'] = get_post_meta( $post_id, 'uacf7_enable_track_order', true ) == 'on' ? 1 : get_post_meta( $post_id, 'uacf7_enable_track_order', true );
						$meta['auto_cart'] = $auto_cart;
					}

					update_post_meta( $post_id, 'uacf7_form_opt', $meta );


				endwhile;
				wp_reset_postdata();
			endif;



			// Option Migration
			$old_option = get_option( 'uacf7_option_name' );
			$new_option = get_option( 'uacf7_settings' ) != '' && is_array( get_option( 'uacf7_settings' ) ) ? get_option( 'uacf7_settings' ) : array();

			$new_option['uacf7_enable_redirection'] = isset( $old_option['uacf7_enable_redirection'] ) && $old_option['uacf7_enable_redirection'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_conditional_field'] = isset( $old_option['uacf7_enable_conditional_field'] ) && $old_option['uacf7_enable_conditional_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_field_column'] = isset( $old_option['uacf7_enable_field_column'] ) && $old_option['uacf7_enable_field_column'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_placeholder'] = isset( $old_option['uacf7_enable_placeholder'] ) && $old_option['uacf7_enable_placeholder'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_uacf7style'] = isset( $old_option['uacf7_enable_uacf7style'] ) && $old_option['uacf7_enable_uacf7style'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_multistep'] = isset( $old_option['uacf7_enable_multistep'] ) && $old_option['uacf7_enable_multistep'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_booking_form'] = isset( $old_option['uacf7_enable_booking_form'] ) && $old_option['uacf7_enable_booking_form'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_post_submission'] = isset( $old_option['uacf7_enable_post_submission'] ) && $old_option['uacf7_enable_post_submission'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_mailchimp'] = isset( $old_option['uacf7_enable_mailchimp'] ) && $old_option['uacf7_enable_mailchimp'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_database_field'] = isset( $old_option['uacf7_enable_database_field'] ) && $old_option['uacf7_enable_database_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_pdf_generator_field'] = isset( $old_option['uacf7_enable_pdf_generator_field'] ) && $old_option['uacf7_enable_pdf_generator_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_conversational_form'] = isset( $old_option['uacf7_enable_conversational_form'] ) && $old_option['uacf7_enable_conversational_form'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_submission_id_field'] = isset( $old_option['uacf7_enable_submission_id_field'] ) && $old_option['uacf7_enable_submission_id_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_telegram_field'] = isset( $old_option['uacf7_enable_telegram_field'] ) && $old_option['uacf7_enable_telegram_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_signature_field'] = isset( $old_option['uacf7_enable_signature_field'] ) && $old_option['uacf7_enable_signature_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_dynamic_text'] = isset( $old_option['uacf7_enable_dynamic_text'] ) && $old_option['uacf7_enable_dynamic_text'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_pre_populate_field'] = isset( $old_option['uacf7_enable_pre_populate_field'] ) && $old_option['uacf7_enable_pre_populate_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_star_rating'] = isset( $old_option['uacf7_enable_star_rating'] ) && $old_option['uacf7_enable_star_rating'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_range_slider'] = isset( $old_option['uacf7_enable_range_slider'] ) && $old_option['uacf7_enable_range_slider'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_repeater_field'] = isset( $old_option['uacf7_enable_repeater_field'] ) && $old_option['uacf7_enable_repeater_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_country_dropdown_field'] = isset( $old_option['uacf7_enable_country_dropdown_field'] ) && $old_option['uacf7_enable_country_dropdown_field'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_ip_geo_fields'] = isset( $old_option['uacf7_enable_ip_geo_fields'] ) && $old_option['uacf7_enable_ip_geo_fields'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_product_dropdown'] = isset( $old_option['uacf7_enable_product_dropdown'] ) && $old_option['uacf7_enable_product_dropdown'] == 'on' ? 1 : 0;
			$new_option['uacf7_enable_product_auto_cart'] = isset( $old_option['uacf7_enable_product_auto_cart'] ) && $old_option['uacf7_enable_product_auto_cart'] == 'on' ? 1 : 0;
			$new_option['uacf7_booking_calendar_key'] = isset( $old_option['uacf7_booking_calendar_key'] ) ? $old_option['uacf7_booking_calendar_key'] : '';
			$new_option['uacf7_booking_calendar_id'] = isset( $old_option['uacf7_booking_calendar_id'] ) ? $old_option['uacf7_booking_calendar_id'] : '';

			// Mailchim api key
			$uacf7_mailchimp_option_name = get_option( 'uacf7_mailchimp_option_name' );
			$new_option['uacf7_mailchimp_api_key'] = isset( $uacf7_mailchimp_option_name['uacf7_mailchimp_api_key'] ) ? $uacf7_mailchimp_option_name['uacf7_mailchimp_api_key'] : '';

			// golobal form style
			$uacf7_global_form_style = get_option( 'uacf7_global_settings_styles' );

			if ( isset( $uacf7_global_form_style ) && ! empty( $uacf7_global_form_style ) ) {
				$uacf7_global_settings_styles_migrate = [];
				foreach ( $uacf7_global_form_style as $key => $value ) {
					$uacf7_global_settings_styles_migrate[ $key ] = $value;
				}
				$new_option = array_merge( $new_option, $uacf7_global_settings_styles_migrate );

			}
			// update migration option
			update_option( 'uacf7_settings', $new_option );
			// update migration status
			update_option( 'uacf7_settings_migration_status', true );
		}


	}
	add_action( 'admin_init', 'uacf7_form_option_Migration_callback' );

}

$plugin_file = 'ultimate-addons-for-contact-form-7/ultimate-addons-for-contact-form-7.php';
add_filter( "in_plugin_update_message-{$plugin_file}", 'uacf7_plugin_update_message', 10, 2 );

function uacf7_plugin_update_message( $plugin_data, $response ) {
	// $new_version = $response->new_version;
	if ( is_object( $response ) && isset( $response->new_version ) ) {
		// If $response is an object
		$new_version = $response->new_version;
	} elseif ( is_array( $response ) && isset( $response['new_version'] ) ) {
		// If $response is an array
		$new_version = $response['new_version'];
	} else {
		// Handle other cases or throw an error
		$new_version = '';
	}

	// var_dump( $response );
	// var_dump( $new_version );

	if ( isset( $new_version ) && version_compare( $new_version, $plugin_data['Version'], '>' ) && $new_version === '3.0.0' ) {
		echo sprintf(
			__( '
				<div class="uacf7_plugin_page_notices" >
					<div class="uacf7_info_wrap">
						<h3>Important update notice!</h3>
						<p>We’ve renamed <strong> Ultimate Addons for Contact Form 7 </strong> to <strong>Ultra Addons for Contact Form 7</strong> as part of a branding update. You’ll now find all settings under the <strong>CF7 Addons</strong>  menu. <a href="https://cf7addons.com/ultra-addons-for-contact-form-7/" target="_blank">Learn More</a></p>
					</div>
					<div class="uacf7_compa_wrap">
						<p>Thank you for your continued support — we\'re excited to keep improving the plugin for you!</p>
					</div>
				</div>
				', 'ultimate-addons-cf7' ),
			'ultimate-addons-cf7'
		);
	}

	return $plugin_data;

}


add_action('wp_ajax_uacf7_install_hydra_booking', 'uacf7_install_hydra_booking');

function uacf7_install_hydra_booking() {
    check_ajax_referer('uacf7_admin_nonce', 'security');

    if (!current_user_can('install_plugins')) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin_slug = 'hydra-booking';
    $plugin_file = 'hydra-booking/hydra-booking.php';

    // Check if already installed
    if (is_plugin_active($plugin_file)) {
        wp_send_json_success(['message' => 'Hydra Booking Plugin is already active.']);
    }

    $api = plugins_api('plugin_information', ['slug' => $plugin_slug]);

    if (is_wp_error($api)) {
        wp_send_json_error(['message' => 'Plugin info could not be retrieved.']);
    }

    $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
    $result = $upgrader->install($api->download_link);

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => 'Hydra Booking Plugin Installation failed.']);
    }

    // Activate plugin
    $activate = activate_plugin($plugin_file);

    if (is_wp_error($activate)) {
        wp_send_json_error(['message' => 'Hydra Booking Plugin Activation failed.']);
    }

    wp_send_json_success(['message' => 'Hydra Booking Plugin installed and activated successfully.']);
}

function uacf7_dismiss_booking_pro_notice() {
    check_ajax_referer('uacf7_admin_nonce', 'security');

    update_option('uacf7_booking_pro_notice_dismissed', true);

    wp_send_json_success();
}

add_action('wp_ajax_uacf7_dismiss_booking_pro_notice', 'uacf7_dismiss_booking_pro_notice');

function uacf7_booking_pro_admin_notice() {
    if (get_option('uacf7_booking_pro_notice_dismissed')) {
        return;
    }

	$last_updated = get_option('uacf7_plugin_last_updated', 0);

	if (time() - $last_updated < 6 * HOUR_IN_SECONDS) {
        return; // If not 6 hours yet, don't show the notice
    }

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'hydra-booking/hydra-booking.php' ) ) {
		return;
	}

    ?>

	<style>
		.hydra-notice{
			padding: 20px;
			background-color: #fff;
			display: flex;
			justify-content: space-between;
			padding: 20px;
			align-items: center;
			gap: 16px;
			border-radius: 8px;
			box-shadow: 0px 8px 30px 0px rgba(16, 40, 20, 0.10);
			width: 50%;
		}

		.hydra-notice .notice-dismiss{
			top: -20px;
			right: -20px;
		}
		.hydra-notice .notice-dismiss::before{
			display: flex;
			width: 24px;
			height: 24px;
			padding: 4px;
			justify-content: center;
			align-items: center;
			gap: 8px;
			border-radius: 32px;
			background: #FFF;
			box-shadow: 0px 8px 16px 0px rgba(16, 40, 20, 0.04);
		}

		.hydra-notice .notice-text strong {
			color: #141915; 
			line-height: 24px; 
			font-size: 15px;
			font-weight: 600;
		}
		.hydra-notice .notice-text p {
			color:  #141915;
			font-size: 13px;
			font-style: normal;
			font-weight: 400;
			line-height: 20px;
			margin: 0;
		}
		.notice.hydra-notice {
			border: 0;
		} 
		.hydra-notice .notice-button {
			display: flex;
			justify-content: space-between;
			flex-direction: column;
			gap: 8px;
			align-items: end;
		}
		.hydra-notice .notice-button p{
			color:  #2E6B38;
			font-size: 13px;
			font-style: normal;
			font-weight: 400;
			line-height: 19px;
			margin: 0;
			display: flex;
			justify-content: flex-start;
			align-items: center;
			gap: 4px;
		}
		.hydra-notice .notice-button p span{
			display: flex;
			gap: 0px;
			justify-content: flex-start;
			align-items: center;
		}
		.hydra-notice .notice-button p span img {
			width: 16px;
		}
		.hydra-notice .notice-button p span img:not(:first-child) {
			margin-left: -5px;
		}
		.hydra-notice .hydra-button {
			padding: 6px 12px;
			border-radius: 6px;
			background: #2E6B38;
			color: #fff;
			font-size: 13px;
			font-style: normal;
			font-weight: 600;
			line-height: 19px; 
			text-decoration: none;
		}


	</style>


    <div class="notice hydra-notice is-dismissible uacf7-booking-pro-notice" style="border-left-color: #b32d2e;">
		<div class="notice-text" style="width: 70%;">
			<strong style="display: block;">Hey <?php echo wp_get_current_user()->display_name; ?>! Want to enhance your Booking/Appointment Addon?</strong>
			<p>HydraBooking: More than Booking Addon, with extra features for your convenience.</p>
		</div>
		<div class="notice-button" style="width: 30%;">
			<p>
				<span>
				<img src="<?php echo UACF7_URL; ?>assets/img/person-1.png" alt="user">
				<img src="<?php echo UACF7_URL; ?>assets/img/person-2.png" alt="person">
				<img src="<?php echo UACF7_URL; ?>assets/img/person-3.png" alt="person">
				</span>	
				Loved by many
			</p>

			<a href="<?php echo admin_url( 'plugin-install.php?tab=search&s=hydra+booking' ) ?>" class="hydra-button">Try Hydra Booking</a>
		</div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('.uacf7-booking-pro-notice').on('click', '.notice-dismiss', function () {
                $.post(ajaxurl, {
                    action: 'uacf7_dismiss_booking_pro_notice',
                    security: '<?php echo wp_create_nonce("uacf7_admin_nonce"); ?>'
                });
            });
        });
    </script>
    <?php
}
add_action('admin_notices', 'uacf7_booking_pro_admin_notice');

/**
 * Migrate Conditional Fields Data
 */

add_action('admin_notices',  'uacf7_migration_notice');
add_action('admin_init',  'uacf7_migrate_conditional_fields_handler');
add_action('admin_notices',  'uacf7_migration_success_notice');
add_action('admin_init', 'uacf7_handle_conditional_notice_dismiss');


function enable_conditional_field() {
	$options = uacf7_settings();

	if ( ! isset( $options['uacf7_enable_conditional_field'] ) || ! $options['uacf7_enable_conditional_field'] ) {
        $options['uacf7_enable_conditional_field'] = true;
        update_option( 'uacf7_settings', $options );
    }
}

function uacf7_migration_notice() {
	if (is_plugin_active('cf7-conditional-fields/conditional-fields.php')) {
		$dismiss_time = get_option('uacf7_migration_done', 0);
		
		if ($dismiss_time === '1' || ($dismiss_time && $dismiss_time > time())) {
			return;
		}

		echo '<div class="notice notice-warning">
			<p><strong>Ultra Addons for Contact Form 7 – Migrate Your Conditional Data:</strong> <br> We\'ve detected conditional data from <strong>Conditional Fields for Contact Form 7</strong>. Easily migrate it with our built-in tool and unlock 40+ powerful addons in one place. Would you like to proceed?</p>
			<p>
				<a href="' . esc_url(admin_url('admin.php?action=uacf7_migrate_conditional_fields')) . '" class="button button-primary">Migrate Now</a>
				<a href="' . esc_url(add_query_arg('uacf7_dismiss_conditional_migration_notice', '1')) . '" class="button button-secondary">Not Now</a>
			</p>
		</div>';
	}
}

function uacf7_handle_conditional_notice_dismiss() {
	if (isset($_GET['uacf7_dismiss_conditional_migration_notice']) && $_GET['uacf7_dismiss_conditional_migration_notice'] === '1') {
		update_option('uacf7_migration_done', time() + (15 * DAY_IN_SECONDS));

		wp_redirect(remove_query_arg('uacf7_dismiss_conditional_migration_notice'));
		exit;
	}
}

function uacf7_migration_success_notice() {
	if (isset($_GET['uacf7_migration_success']) && $_GET['uacf7_migration_success'] == 1) {
		echo '<div class="notice notice-success is-dismissible">
			<p>Migration completed successfully.</p>
		</div>';
	}
}

function uacf7_migrate_conditional_fields_handler() {
	if (isset($_GET['action']) && $_GET['action'] === 'uacf7_migrate_conditional_fields') {
		enable_conditional_field();
		uacf7_migrate_conditional_fields();

		update_option('uacf7_migration_done', true);
		wp_redirect(admin_url('admin.php?page=wpcf7&uacf7_migration_success=1'));
		exit;
	}
}

function uacf7_migrate_conditional_fields() {
	$forms = get_posts([
		'post_type' => 'wpcf7_contact_form',
		'posts_per_page' => -1,
	]);

	foreach ($forms as $form) {
		$post_id = $form->ID;

		// Migrate conditional metadata
		$conditional_data = get_post_meta($post_id, 'wpcf7cf_options', true);
		$operator_map = [
			'equals' => 'equal',
			'not equals' => 'not_equal',
			'greater than' => 'greater_than',
			'greater than or equals' => 'greater_than_or_equal_to',
			'less than' => 'less_than',
			'less than or equals' => 'less_than_or_equal_to',
			'is empty' => 'contains',
			'not empty' => 'does_not_contain',
		];
		
		if (!empty($conditional_data)) {
			$migrated_data = [];

			foreach ($conditional_data as $index => $condition) {
				$then_field = $condition['then_field'];
				$condition_for = count($condition['and_rules']) > 1 ? 'all' : 'any';

				$uacf7_conditions = [];

				foreach ($condition['and_rules'] as $rule_index => $rule) {
					$mapped_operator = isset($operator_map[$rule['operator']]) 
						? $operator_map[$rule['operator']] 
						: $rule['operator']; 
				
					$uacf7_conditions[$rule_index + 1] = [
						'uacf7_cf_tn' => $rule['if_field'],
						'uacf7_cf_operator' => $mapped_operator,
						'uacf7_cf_val' => $rule['if_value'],
					];
				}

				$migrated_data[$index + 1] = [
					'uacf7_cf_group' => $then_field,
					'uacf7_cf_hs' => 'show',
					'uacf7_cf_condition_for' => $condition_for,
					'uacf7_cf_conditions' => $uacf7_conditions,
				];
			}

			// Fetch existing form options to maintain other settings
			$form_options = get_post_meta($post_id, 'uacf7_form_opt', true);
			if (!is_array($form_options)) {
				$form_options = [];
			}

			// Set migrated conditionals under 'conditional' key
			$form_options['conditional'] = [
				'conditional_heading' => '',
				'conditional_field_docs' => '',
				'conditional_form_options_heading' => '',
				'conditional_repeater' => $migrated_data,
			];

			// Save the updated form options back to post meta
			update_post_meta($post_id, 'uacf7_form_opt', $form_options);
		}

		// Replace [group] tags with [conditional] in form content
		$form_content = get_post_meta($post_id, '_form', true);

		if (!empty($form_content)) {
			$updated_content = preg_replace_callback(
				'/\[group\s+([^\]]+)\](.*?)\[\/group\]/s',
				function ($matches) {
					$group_name = $matches[1];
					$content_inside = $matches[2];
					return "[conditional {$group_name}]{$content_inside}[/conditional]";
				},
				$form_content
			);

			// Save updated form content if changed
			if ($updated_content !== $form_content) {
				update_post_meta($post_id, '_form', $updated_content);
			}
		}
	}
}


add_action('wpcf7_before_send_mail', 'uacf7_preserve_line_breaks');

function uacf7_preserve_line_breaks($contact_form) {
    $submission = WPCF7_Submission::get_instance();

    if (!$submission) {
        return;
    }

    $properties = $contact_form->get_properties();
    
    $is_html = !empty($properties['mail']['use_html']);
	
	if($is_html){
		if (!empty($properties['mail']['body'])) {
			$properties['mail']['body'] = wpautop($properties['mail']['body']);
		}
	
		if (!empty($properties['mail_2']['body'])) {
			$properties['mail_2']['body'] = wpautop($properties['mail_2']['body']);
		}
	}

	$contact_form->set_properties($properties);
}


add_action('admin_footer', 'uacf7_show_hydra_modal');

function uacf7_show_hydra_modal() {

    if (!isset($_GET['page']) || $_GET['page'] !== 'uacf7_addons') {
        return;
    }

    $user_id = get_current_user_id();

	if (!current_user_can('install_plugins')) {
        return;
    }
	
    $modal_shown = get_user_meta($user_id, 'uacf7_modal_shown', true);

    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    $plugin_slug = 'hydra-booking/hydra-booking.php';
    $plugin_installed = file_exists(WP_PLUGIN_DIR . '/hydra-booking'); 
    $plugin_activated = is_plugin_active($plugin_slug);

    if ($plugin_activated || $modal_shown) {
        return;
    }

    ?>
    <div id="uacf7-modal" class="uacf7-modal">
        <div class="uacf7-modal-content">
            <span id="uacf7-modal-close" class="uacf7-modal-close">&times;</span>
            <h2>Hey <?php echo wp_get_current_user()->display_name; ?>! Want to make your Booking/Appointment Addon stand out?</h2>
            <p>HydraBooking offers everything you love about the Booking Addon—plus powerful new features designed to make your life easier.</p>
            <div class="hydra-modal-users">
                <div class="users">
                    <span>
                        <img src="<?php echo UACF7_URL; ?>assets/img/person-1.png" alt="user">
                        <img src="<?php echo UACF7_URL; ?>assets/img/person-2.png" alt="person">
                        <img src="<?php echo UACF7_URL; ?>assets/img/person-3.png" alt="person">
                    </span>    
                    Many people are already using this...
                </div>
            </div>
            <button id="uacf7-install-plugin">Try HydraBooking</button>
        </div>
    </div>

    <script>
    jQuery(document).ready(function ($) {
		$('#uacf7-modal').fadeIn();

		$('#uacf7-modal-close').click(function () {
			$('#uacf7-modal').fadeOut();
			$.post('<?php echo admin_url('admin-ajax.php'); ?>', {
				action: 'uacf7_set_modal_shown',
				nonce: '<?php echo wp_create_nonce("uacf7_modal_nonce"); ?>'
			});
		});

		$('#uacf7-install-plugin').click(function () {
			let $button = $(this);
			showLoading($button, 'Installing...');

			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					action: 'install_hydrabooking',
					nonce: '<?php echo wp_create_nonce("install_hydra_booking"); ?>'
				},
				success: function (response) {
					if (response.success) {
						showSuccess($button, 'Installed ✅');
						setTimeout(() => {
							showLoading($button, 'Activating...');
							activateHydraBooking($button);
						}, 1000);
					} else {
						showError($button, 'Installation Failed! Try Again');
					}
				},
				error: function () {
					showError($button, 'Installation Failed! Try Again');
				}
			});
		});

		function activateHydraBooking($button) {
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					action: 'activate_hydrabooking',
					nonce: '<?php echo wp_create_nonce("activate_hydra_booking"); ?>'
				},
				success: function (response) {
					if (response.success) {
						showSuccess($button, 'Activated 🎉');
					} else {
						showError($button, 'Activation Failed! Try Again');
					}
				},
				error: function () {
					showError($button, 'Activation Failed! Try Again');
				}
			});
		}

		function showLoading($button, text) {
			$button.html(`<span class="loader"></span> ${text}`).prop('disabled', true);
		}

		function showSuccess($button, text) {
			$button.html(text).prop('disabled', true);
		}

		function showError($button, text) {
			$button.html(text).prop('disabled', false);
		}
	});
    </script>
    <?php
}


function install_hydrabooking() {
    check_ajax_referer('install_hydra_booking', 'nonce');

    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';

    $plugin_slug = 'hydra-booking';

    // Fetch plugin information
    $api = plugins_api('plugin_information', ['slug' => $plugin_slug]);
    if (is_wp_error($api)) {
        wp_send_json_error(['message' => 'Plugin info could not be retrieved.']);
    }

    // Install the plugin
    $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    $installed = $upgrader->install($api->download_link);

    if (is_wp_error($installed)) {
        wp_send_json_error(['message' => 'Plugin installation failed.']);
    }

    wp_send_json_success(['message' => 'Plugin installed successfully.']);
}
add_action('wp_ajax_install_hydrabooking', 'install_hydrabooking');


function activate_hydrabooking() {
    check_ajax_referer('activate_hydra_booking', 'nonce');

    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin_file = 'hydra-booking/hydra-booking.php';

    // Activate the plugin
    $activated = activate_plugin($plugin_file);

    if (is_wp_error($activated)) {
        wp_send_json_error(['message' => 'Plugin activation failed.']);
    }

    wp_send_json_success(['message' => 'Plugin activated successfully.']);
}
add_action('wp_ajax_activate_hydrabooking', 'activate_hydrabooking');


function uacf7_set_modal_shown() {
    check_ajax_referer('uacf7_modal_nonce', 'nonce');

    $user_id = get_current_user_id();
    update_user_meta($user_id, 'uacf7_modal_shown', 1);

    wp_send_json_success(['message' => 'Modal status updated.']);
}
add_action('wp_ajax_uacf7_set_modal_shown', 'uacf7_set_modal_shown');



add_action('admin_notices', 'uacf7_redirection_migration_notice');
add_action('admin_init', 'uacf7_migrate_redirection_handler');
add_action('admin_notices', 'uacf7_redirection_migration_success_notice');
add_action('admin_init', 'uacf7_handle_redirection_dismiss_notice');

/**
 * Show the migration notice if "Redirection for Contact Form 7" is active.
 */
function uacf7_redirection_migration_notice() {
	if (is_plugin_active('wpcf7-redirect/wpcf7-redirect.php')) {
		$dismiss_time = get_option('uacf7_redirection_migration_done', 0);
		
		if ($dismiss_time == '1' || ($dismiss_time && $dismiss_time > time())) {
			return;
		}

		echo '<div class="notice notice-warning">
			<p><strong>Ultra Addons for Contact Form 7 – Migrate Your Redirection Settings:</strong><br> We\'ve detected redirection settings from <strong>Redirection for Contact Form 7</strong>. Easily migrate them with our built-in tool—no need for multiple plugins! Plus, access 40+ powerful addons in one place. Would you like to proceed?</p>
			<p>
				<a href="' . esc_url(admin_url('admin.php?action=uacf7_migrate_redirection')) . '" class="button button-primary">Migrate Now</a>
				<a href="' . esc_url(add_query_arg('uacf7_dismiss_redirection_notice', '1')) . '" class="button button-secondary">Not Now</a>
			</p>
		</div>';
	}
}

function uacf7_handle_redirection_dismiss_notice() {
	if (isset($_GET['uacf7_dismiss_redirection_notice']) && $_GET['uacf7_dismiss_redirection_notice'] === '1') {
		update_option('uacf7_redirection_migration_done', time() + (15 * DAY_IN_SECONDS));
		wp_redirect(remove_query_arg('uacf7_dismiss_redirection_notice'));
		exit;
	}
}

function enable_redirection_field() {
	$options = uacf7_settings();

	if ( ! isset( $options['uacf7_enable_redirection'] ) || ! $options['uacf7_enable_redirection'] ) {
        $options['uacf7_enable_redirection'] = true;
        update_option( 'uacf7_settings', $options );
    }
}

/**
 * Show success notice after successful migration.
 */
function uacf7_redirection_migration_success_notice() {
	if (isset($_GET['uacf7_redirection_migration_success']) && $_GET['uacf7_redirection_migration_success'] == 1) {
		echo '<div class="notice notice-success is-dismissible">
			<p>Redirection migration completed successfully.</p>
		</div>';
	}
}

/**
 * Handle the migration process when "Migrate Now" button is clicked.
 */
function uacf7_migrate_redirection_handler() {
	if (isset($_GET['action']) && $_GET['action'] === 'uacf7_migrate_redirection') {
		enable_redirection_field();
		migrate_redirection_data_to_uacf7();

		update_option('uacf7_redirection_migration_done', true);
		wp_redirect(admin_url('admin.php?page=wpcf7&uacf7_redirection_migration_success=1'));
		exit;
	}
}

function migrate_redirection_data_to_uacf7() {

	$redirect_actions = get_posts([
		'post_type' => 'wpcf7r_action',
		'post_status' => 'private',
		'posts_per_page' => -1,
	]);
	
	foreach ($redirect_actions as $action) {
		$action_id = $action->ID;
		$meta_data = get_post_custom($action_id, true);

		if (empty($meta_data['wpcf7_id'][0])) {
			continue;
		}

		$wpcf7_id = $meta_data['wpcf7_id'][0];

		$action_type = isset($meta_data['action_type'][0]) ? $meta_data['action_type'][0] : '';
		if ($action_type !== 'redirect') {
			continue;
		}

		unset($meta_data['uacf7_form_opt']);
		
		$redirect_data = [
			'redirect_enabled' => ($meta_data['action_status'][0] === 'on') ? 1 : 0,
			'external_url' => !empty($meta_data['use_external_url'][0]) == 'on' ? $meta_data['external_url'][0] : '',
			'redirect_delay' => !empty($meta_data['delay_redirect_seconds'][0]) ? intval($meta_data['delay_redirect_seconds'][0]) : 0,
			'redirection_heading' => '',
			'redirection_docs' => '',
			'uacf7_redirect_enable' => ($meta_data['action_status'][0] === 'on') ? 1 : 0,
			'uacf7_redirect_form_options_heading' => '',
			'uacf7_redirect_to_type' => !empty($meta_data['use_external_url'][0]) == 'on' ? 'to_url' : 'to_page',
			'page_id' => !empty($meta_data['page_id'][0]) ? intval($meta_data['page_id'][0]) : 0,
			'uacf7_redirect_type' => '',
			'target' => !empty($meta_data['open_in_new_tab'][0]) && $meta_data['open_in_new_tab'][0] === 'on' ? 1 : 0,
			'uacf7_redirect_tag_support' => '',
		];

		if (!empty($meta_data['http_build_query_selectively_fields'][0])) {
			$redirect_data['conditional_redirect'] = [
				1 => [
					'uacf7_cr_tn' => '0',
					'uacf7_cr_field_val' => 'Example',
					'uacf7_cr_redirect_to_url' => 'https://example.com',
				],
			];
		}

		$form_options = get_post_meta($wpcf7_id, 'uacf7_form_opt', true);
		if (!is_array($form_options)) {
			$form_options = [];
		}

		$form_options['redirection'] = $redirect_data;

		update_post_meta($wpcf7_id, 'uacf7_form_opt', $form_options);

	}

}



// function uacf7_check_and_install_hydra_booking($upgrader_object, $options) {
	
// 	if ($options['action'] !== 'update' || $options['type'] !== 'plugin') {
// 		return;
// 	}

// 	$ultimate_addons_slug = 'ultimate-addons-for-contact-form-7/ultimate-addons-for-contact-form-7.php';

// 	if (empty($options['plugins']) || !is_array($options['plugins'])) {
// 		return;
// 	}

// 	if (!in_array($ultimate_addons_slug, $options['plugins'])) {
// 		return;
// 	}

//     $options = uacf7_settings();

//     if (!isset($options['uacf7_enable_booking_form']) || !$options['uacf7_enable_booking_form']) {
//         return;
//     }

//     $hydra_plugin_slug = 'hydra-booking/hydra-booking.php';
//     if (is_plugin_active($hydra_plugin_slug) || file_exists(WP_PLUGIN_DIR . '/' . $hydra_plugin_slug)) {
//         return; 
//     }

//     uacf7_install_hydra_booking_on_plugin_update();
// }

// add_action('upgrader_process_complete', 'uacf7_check_and_install_hydra_booking', 10, 2);

// function uacf7_install_hydra_booking_on_plugin_update() {
//     if (!current_user_can('install_plugins')) {
//         return;
//     }

//     include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
//     include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
//     include_once ABSPATH . 'wp-admin/includes/plugin.php';

//     $plugin_slug = 'hydra-booking';
//     $plugin_file = 'hydra-booking/hydra-booking.php';

//     $api = plugins_api('plugin_information', ['slug' => $plugin_slug]);

//     if (is_wp_error($api) || empty($api->download_link)) {
//         return;
//     }

//     $upgrader = new Plugin_Upgrader(new WP_Upgrader_Skin());
//     $result = $upgrader->install($api->download_link);

//     if (is_wp_error($result)) {
//         return; 
//     }

//     activate_plugin($plugin_file);
// }




