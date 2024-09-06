<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_SPAM_PROTECTION {

	public function __construct() {

		add_action( 'wpcf7_init', array( $this, 'uacf7_spam_protection_add_shortcodes' ), 5, 10 );
		add_action( 'admin_init', array( $this, 'uacf7_spam_protection_tag_generator' ) );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_spam_protection' ), 34, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'uacf7_spam_protection_scripts' ), 5, 10 );

		// add_filter( 'wpcf7_load_js', '__return_false' );
	}

	public function uacf7_spam_protection_scripts() {
		$option = uacf7_settings();
		$spam_protection_pro = ( isset( $option['uacf7_enable_spam_protection_pro'] ) && $option['uacf7_enable_spam_protection_pro'] == '1' ) ? true : false;
		wp_register_script( 'uacf7-spam-protection-arithmetic', UACF7_URL . 'addons/spam-protection/assets/js/spam-protection-arithmetic.js', [ 'jquery' ], WPCF7_VERSION, true );
		wp_register_script( 'uacf7-spam-protection-image', UACF7_URL . 'addons/spam-protection/assets/js/spam-protection-image.js', [ 'jquery' ], WPCF7_VERSION, true );
		wp_enqueue_style( 'uacf7-spam-protection-css', UACF7_URL . 'addons/spam-protection/assets/css/spam-protection-style.css', [], WPCF7_VERSION, 'all' );

		// Localize the script to pass PHP data to JavaScript
		wp_localize_script(
			'uacf7-spam-protection-arithmetic', // The handle of the script to localize
			'uacf7_spam_protection_settings',  // Name of the JavaScript object
			[ 
				'enable_spam_protection_pro' => $spam_protection_pro, // Data to pass
				'captchaARequiredMessage' => __( 'CAPTCHA field is required. Please enter the answer.', 'ultimate-addons-cf7' ),
				'captchaValidatedMessage' => __( 'CAPTCHA validated successfully.', 'ultimate-addons-cf7' ),
				'captchaValidationFailed' => __( 'CAPTCHA validation failed. Please try again.', 'ultimate-addons-cf7' ),
			]
		);
		// Localize the script to pass PHP data to JavaScript
		wp_localize_script(
			'uacf7-spam-protection-image', 'uacf7_spam_protection_settings', [ 
				'enable_spam_protection_pro' => $spam_protection_pro, // Data to pass
				'captchaRequiredMessage' => __( 'CAPTCHA field is required. Please enter the answer.', 'ultimate-addons-cf7' ),
				'captchaSuccessMessage' => __( 'CAPTCHA validated successfully.', 'ultimate-addons-cf7' ),
				'captchaFailedMessage' => __( 'CAPTCHA validation failed. Please try again.', 'ultimate-addons-cf7' ),
			]
		);

	}


	public function uacf7_post_meta_options_spam_protection( $value, $post_id ) {
		$spam_protection = apply_filters( 'uacf7_post_meta_options_spam_protection_pro', $data = array(
			'title' => __( 'Spam Protection', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-spaghetti-monster-flying',
			'checked_field' => 'uacf7_spam_protection_enable',

			'fields' => array(
				'uacf7_spam_protection_heading' => array(
					'id' => 'uacf7_spam_protection_heading',
					'type' => 'heading',
					'label' => __( 'Spam Protection Settings', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'This feature will help you to protect your form submission from Spam attacks.', 'ultimate-addons-cf7' ),
					'content' => sprintf(
						// Translators: %1$s is replaced with the link to documentation.
						esc_html__( 'Add spam protection for your contact form 7 forms. %s .', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/spam-protection/" target="_blank">See Demo</a>',

					),
				),

				array(
					'id' => 'spam-protection-docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						// Translators: %1$s is replaced with the link to documentation. 
						esc_html__( 'Not sure how to set this? Check our step-by-step documentation on  %s .', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/spam-protection/" target="_blank">Spam Protection</a>',
					),
				),

				'uacf7_spam_protection_enable' => array(
					'id' => 'uacf7_spam_protection_enable',
					'type' => 'switch',
					'label' => __( 'Enable Spam Protection', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false
				),
				'uacf7_spam_protection_type' => array(
					'id' => 'uacf7_spam_protection_type',
					'type' => 'select',
					'label' => __( 'Protection Type', 'ultimate-addons-cf7' ),
					'options' => array(
						'arithmathic_recognation' => 'Arithmetic Recognition',
						'image_recognation' => 'Image Recognition',
					),
					'default' => 'arithmathic_recognation'
				),
				'uacf7_minimum_time_limit' => array(
					'id' => 'uacf7_minimum_time_limit',
					'type' => 'number',
					'label' => __( 'Each Submission Difference', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'To prevent spamming bots, you can set a time limit to restrict too frequent submissions. Please specify the time limit in seconds. Default: 5 seconds', 'ultimate-addons-cf7' ),
					'placeholder' => __( '5', 'ultimate-addons-cf7' ),
					'default' => 5,
					'is_pro' => true
				),
				// 'uacf7_word_filter' => array(
				// 	'id' => 'uacf7_word_filter',
				// 	'type' => 'textarea',
				// 	'label' => __('Word Filtering', 'ultimate-addons-cf7'),
				// 	'subtitle' => __('Enlist the words you want to avoid from Spammer, Separate the words using a Comma. If that word/s found in the message it will skip to the email (email will not send to mail)', 'ultimate-addons-cf7'),
				// 	'placeholder' => __('E.g. evil, earning money, scam', 'ultimate-addons-cf7'),
				// 	'is_pro' => true
				// ),
				// 'uacf7_ip_block' => array(
				// 	'id' => 'uacf7_ip_block',
				// 	'type' => 'textarea',
				// 	'label' => __( 'IP Block', 'ultimate-addons-cf7' ),
				// 	'subtitle' => __( 'Enlist the IP you want to Ban / Block, Separate the IPs using a Comma', 'ultimate-addons-cf7' ),
				// 	'placeholder' => __( 'E.g. 192.158.1.38,192.158.1.39,192.158.1.40', 'ultimate-addons-cf7' ),
				// 	'is_pro' => true
				// ),
				// 'uacf7_blocked_countries' => array(
				// 	'id' => 'uacf7_blocked_countries',
				// 	'type' => 'textarea',
				// 	'label' => __( 'Country Block', 'ultimate-addons-cf7' ),
				// 	'subtitle' => sprintf(
				// 		// Translators: %1$s is replaced with the link to documentation.
				// 		esc_html__( 'Enlist the Country or Countries that you want to Ban / Block. Separate the Countries %s using a Comma', 'ultimate-addons-cf7' ),
				// 		'<a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements" target="_blank">' . __( 'iso2 name', 'ultimate-addons-cf7' ) . '</a>'
				// 	),
				// 	'placeholder' => __( 'E.g. us,ca,uk', 'ultimate-addons-cf7' ),
				// 	'is_pro' => true
				// ),

			)

		), $post_id );

		$value['spam_protection'] = $spam_protection;
		return $value;
	}


	public function uacf7_spam_protection_tag_generator() {
		wpcf7_add_tag_generator(
			'uacf7_spam_protection',
			__( 'Spam Protection', 'ultimate-addons-cf7' ),
			'uacf7-tg-pane-spam-protection',
			array( $this, 'tg_pane_spam_protection' )
		);
	}


	public static function tg_pane_spam_protection( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		$uacf7_field_type = 'uacf7_spam_protection';
		?>
		<div class="control-box">
			<fieldset>
				<table class="form-table">
					<tbody>
						<div class="uacf7-doc-notice">
							<?php echo sprintf(
								// Translators: %1$s is replaced with the link to documentation. 
								esc_html__( 'Not sure how to set this? Check our step by step  %1s.', 'ultimate-addons-cf7' ),
								'<a href="https://themefic.com/docs/uacf7/free-addons/spam-protection/" target="_blank">documentation</a>'
							); ?>
						</div>
						<tr>
							<th scope="row"><label
									for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'ultimate-addons-cf7' ) ); ?></label>
							</th>
							<td><input type="text" name="name" class="tg-name oneline"
									id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label
									for="tag-generator-panel-text-class"><?php echo esc_html__( 'Class attribute', 'ultimate-addons-cf7' ); ?></label>
							</th>
							<td><input type="text" name="class" class="classvalue oneline option"
									id="tag-generator-panel-text-class"></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="insert-box">
			<input type="text" name="<?php echo esc_attr( $uacf7_field_type ); ?>" class="tag code" readonly="readonly"
				onfocus="this.select()" />
			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" id="prevent_multiple"
					value="<?php echo esc_attr( __( 'Insert Tag', 'ultimate-addons-cf7' ) ); ?>" />
			</div>
		</div>
		<?php
	}

	public function uacf7_spam_protection_add_shortcodes() {
		wpcf7_add_form_tag(
			array( 'uacf7_spam_protection', 'uacf7_spam_protection*' ),
			array( $this, 'uacf7_spam_protection_tag_handler_callback' ),
			array( 'name-attr' => true )
		);
	}

	public function uacf7_spam_protection_tag_handler_callback( $tag ) {

		if ( empty( $tag->name ) ) {
			return 'Tag not Found!';
		}

		/** Enable / Disable Spam Protection */
		$wpcf7 = WPCF7_ContactForm::get_current();
		$formid = $wpcf7->id();

		$uacf7_spam_protection = uacf7_get_form_option( $formid, 'spam_protection' );

		if ( isset( $uacf7_spam_protection['uacf7_spam_protection_enable'] ) && $uacf7_spam_protection['uacf7_spam_protection_enable'] != '1' ) {
			return;
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );


		if ( $validation_error ) {
			$class .= 'wpcf7-not-valid';
		}

		$atts = array();


		$ip = ( isset( $_SERVER['X_FORWARDED_FOR'] ) ) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$addr = wp_remote_get( 'http://ip-api.com/php/' . $ip );
		$addr_body = wp_remote_retrieve_body( $addr );
		$addr = unserialize( $addr_body );

		$atts['iso2'] = isset( $addr['countryCode'] );
		$atts['id'] = $tag->get_id_option();

		//Conditionally Loading Scripts
		if ( is_array( $uacf7_spam_protection ) && isset( $uacf7_spam_protection['uacf7_spam_protection_type'] ) ) {
			$atts['protection-method'] = $uacf7_spam_protection['uacf7_spam_protection_type'];

			// Conditionally Loading Scripts
			if ( $uacf7_spam_protection['uacf7_spam_protection_type'] === 'arithmathic_recognation' ) {
				wp_enqueue_script( 'uacf7-spam-protection-arithmetic' );
			}

			if ( $uacf7_spam_protection['uacf7_spam_protection_type'] === 'image_recognation' ) {
				wp_enqueue_script( 'uacf7-spam-protection-image' );
			}
		} else {
			$atts['protection-method'] = 'none';
		}

		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
		$atts['name'] = $tag->name;
		$atts['user-ip'] = $ip;
		$value = $tag->values;
		$default_value = $tag->get_default_option( $value );
		$atts['value'] = $value;
		$atts = wpcf7_format_atts( $atts );


		ob_start();

		?>
		<span class="wpcf7-form-control-wrap <?php echo sanitize_html_class( $tag->name ); ?>"
			data-name="<?php echo sanitize_html_class( $tag->name ); ?>">
			<div class="uacf7_spam_recognation" <?php echo esc_attr( $atts ); ?>>
				<?php if ( isset( $uacf7_spam_protection['uacf7_spam_protection_type'] ) && $uacf7_spam_protection['uacf7_spam_protection_type'] === 'arithmathic_recognation' ) { ?>
					<div id="arithmathic_recognation">
						<div id="arithmetic_input_holder">
							<div id="arithmetic_cal">
								<span id="frn">5</span>
								+
								<span id="srn">6</span>
								=
							</div>
							<button id="arithmathic_refresh">
								<svg xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
									<path
										d="M142.9 142.9c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5c0 0 0 0 0 0H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5c7.7-21.8 20.2-42.3 37.8-59.8zM16 312v7.6 .7V440c0 9.7 5.8 18.5 14.8 22.2s19.3 1.7 26.2-5.2l41.6-41.6c87.6 86.5 228.7 86.2 315.8-1c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.2 62.2-162.7 62.5-225.3 1L185 329c6.9-6.9 8.9-17.2 5.2-26.2s-12.5-14.8-22.2-14.8H48.4h-.7H40c-13.3 0-24 10.7-24 24z" />
								</svg>
							</button>
							<input type="number" min="0" id="rtn"
								placeholder="<?php esc_attr_e( 'Enter CAPTCHA answer', 'ultimate-addons-cf7' ); ?>" value="">
						</div>
						<div>

						</div>
						<div id="arithmathic_result"></div>
					</div>
				<?php } else if ( isset( $uacf7_spam_protection['uacf7_spam_protection_type'] ) && $uacf7_spam_protection['uacf7_spam_protection_type'] === 'image_recognation' ) { ?>
						<div id="image_recognation">
							<div id="captcha_input_holder">
								<div id="captcha"></div> <button id="arithmathic_refresh">
									<svg xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
										<path
											d="M142.9 142.9c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5c0 0 0 0 0 0H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5c7.7-21.8 20.2-42.3 37.8-59.8zM16 312v7.6 .7V440c0 9.7 5.8 18.5 14.8 22.2s19.3 1.7 26.2-5.2l41.6-41.6c87.6 86.5 228.7 86.2 315.8-1c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.2 62.2-162.7 62.5-225.3 1L185 329c6.9-6.9 8.9-17.2 5.2-26.2s-12.5-14.8-22.2-14.8H48.4h-.7H40c-13.3 0-24 10.7-24 24z" />
									</svg>
								</button>
								<input type="text" id="userInput"
									placeholder="<?php esc_attr_e( 'Enter CAPTCHA answer', 'ultimate-addons-cf7' ); ?>">

							</div>
							<div>
							</div>
							<div id="result"></div>
						</div>
				<?php } else { ?>
						<p>No Protection is applied</p>
				<?php } ?>

			</div>
		</span>
		<?php

		$spam_protection_buffer = ob_get_clean();

		return $spam_protection_buffer;

		// return apply_filters( 'uacf7_range_slider_style_pro_feature', $spam_protection_buffer, $tag); 

	}
}

new UACF7_SPAM_PROTECTION();
