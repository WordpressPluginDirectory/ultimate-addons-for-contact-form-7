<?php

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	echo "You are not allowed to access directly";
	exit();
}

class UACF7_SIGNATURE {

	public function __construct() {
		// require_once 'inc/signature.php';
		add_action( 'wp_enqueue_scripts', [ $this, 'uacf7_signature_public_scripts' ] );

		add_action( 'admin_init', [ $this, 'uacf7_signature_tag_generator' ] );
		add_action( 'wpcf7_init', [ $this, 'uacf7_signature_add_shortcodes' ] );

		add_filter( 'wpcf7_validate_uacf7_signature', [ $this, 'uacf7_signature_validation_filter' ], 10, 2 );
		add_filter( 'wpcf7_validate_uacf7_signature*', [ $this, 'uacf7_signature_validation_filter' ], 10, 2 );

		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_signature' ), 30, 2 );

		//  add_filter( 'wpcf7_load_js', '__return_false' );
	}

	/** Loading Scripts */

	public function uacf7_signature_public_scripts() {

		wp_enqueue_script( 'uacf7-signature-public-assets', UACF7_URL . 'addons/signature/assets/public/js/signature.js', [ 'jquery' ], 'UACF7_VERSION', true );
		wp_enqueue_script( 'uacf7-sign-lib.min', UACF7_URL . 'addons/signature/assets/public/js/sign-lib.min.js', [ 'jquery' ], 'UACF7_VERSION', true );


		wp_localize_script( 'uacf7-signature-public-assets', 'uacf7_sign_obj', [ 

			'message_notice' => __( 'Please sign first and confirm your signature before form submission', 'ultimate-addons-cf7' ),
			'message_success' => __( 'Signature Confirmed', 'ultimate-addons-cf7' ),

		] );

	}



	public function uacf7_post_meta_options_signature( $value, $post_id ) {

		$signature = apply_filters( 'uacf7_post_meta_options_signature_pro', $data = array(
			'title' => __( 'Digital Signature', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-signature',
			'checked_field' => 'uacf7_signature_enable',
			'fields' => array(

				'uacf7_sign_heading' => array(
					'id' => 'uacf7_sign_heading',
					'type' => 'heading',
					'label' => __( 'Signature Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Add a digital signature feature to your forms. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/contact-form-7-signature-addon/" target="_blank" rel="noopener">Example</a>'
					)
				),
				'signature_docs' => array(
					'id' => 'signature_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-signature-addon/" target="_blank" rel="noopener">Digital Signature</a>'
					)
				),

				'uacf7_signature_enable' => array(
					'id' => 'uacf7_signature_enable',
					'type' => 'switch',
					'label' => __( ' Enable Signature', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false
				),
				'uacf7_signature_form_options_heading' => array(
					'id' => 'uacf7_signature_form_options_heading',
					'type' => 'heading',
					'label' => __( 'Signature Option ', 'ultimate-addons-cf7' ),
				),
				'uacf7_signature_bg_color' => array(
					'id' => 'uacf7_signature_bg_color',
					'type' => 'color',
					'label' => __( 'Signature Pad Background Color', 'ultimate-addons-cf7' ),
					'description' => __( 'E.g. Default is #dddddd', 'ultimate-addons-cf7' ),
					'default' => '#dddddd',
					'field_width' => 50,
				),
				'uacf7_signature_pen_color' => array(
					'id' => 'uacf7_signature_pen_color',
					'type' => 'color',
					'label' => __( 'Signature Pen Color', 'ultimate-addons-cf7' ),
					'description' => __( 'E.g. Default is #000000', 'ultimate-addons-cf7' ),
					'default' => '#000000',
					'field_width' => 50,
				),

				'uacf7_signature_pad_width' => array(
					'id' => 'uacf7_signature_pad_width',
					'type' => 'number',
					'label' => __( 'Signature Pad Width', 'ultimate-addons-cf7' ),
					'description' => __( 'E.g. There is no need to include units such as "px" or "rem".', 'ultimate-addons-cf7' ),
					'default' => '300',
					'field_width' => 50,
				),
				'uacf7_signature_pad_height' => array(
					'id' => 'uacf7_signature_pad_height',
					'type' => 'number',
					'label' => __( 'Signature Pad Height', 'ultimate-addons-cf7' ),
					'description' => __( 'E.g. There is no need to include units such as "px" or "rem".', 'ultimate-addons-cf7' ),
					'default' => '100',
					'field_width' => 50,
				),

			),


		), $post_id );

		$value['signature'] = $signature;
		return $value;
	}


	/** Add Signature Shortcode */

	public function uacf7_signature_add_shortcodes() {
		wpcf7_add_form_tag(
			array( 'uacf7_signature', 'uacf7_signature*' ),
			array( $this, 'uacf7_signature_tag_handler_callback' ),
			array(
				'name-attr' => true,
				'file-uploading' => true
			)
		);
	}

	public function uacf7_signature_tag_handler_callback( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$wpcf7 = WPCF7_ContactForm::get_current();
		$formid = $wpcf7->id();

		$uacf7_signature_settings = uacf7_get_form_option( $formid, 'signature' );

		$uacf7_signature_enable = isset( $uacf7_signature_settings['uacf7_signature_enable'] ) ? $uacf7_signature_settings['uacf7_signature_enable'] : '';
		$bg_color = isset( $uacf7_signature_settings['uacf7_signature_bg_color'] ) ? $uacf7_signature_settings['uacf7_signature_bg_color'] : '';
		$pen_color = isset( $uacf7_signature_settings['uacf7_signature_pen_color'] ) ? $uacf7_signature_settings['uacf7_signature_pen_color'] : '';
		$canvas_width = isset( $uacf7_signature_settings['uacf7_signature_pad_width'] ) ? $uacf7_signature_settings['uacf7_signature_pad_width'] : '';
		$canvas_height = isset( $uacf7_signature_settings['uacf7_signature_pad_height'] ) ? $uacf7_signature_settings['uacf7_signature_pad_height'] : '';


		if ( $uacf7_signature_enable != '1' || $uacf7_signature_enable === '' ) {
			return;
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );
		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();
		$atts['pen-color'] = esc_attr( $pen_color );
		$atts['bg-color'] = esc_attr( $bg_color );
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
		$atts['name'] = $tag->name;
		$atts = wpcf7_format_atts( $atts );

		ob_start();

		?>
		<span class="wpcf7-form-control-wrap <?php echo sanitize_html_class( $tag->name ); ?>"
			data-name="<?php echo sanitize_html_class( $tag->name ); ?>">
			<input hidden type="file" class="img_id_special" <?php echo $atts; ?>>

			<div class="signature-pad" data-field-name="<?php echo sanitize_html_class( $tag->name ); ?>">
				<canvas id="<?php echo sanitize_html_class( $tag->name ); ?>"
					data-field-name="<?php echo sanitize_html_class( $tag->name ); ?>" width="<?php echo $canvas_width; ?>"
					height="<?php echo $canvas_height; ?>"></canvas>
			</div>
			<div class="control_div">
				<button data-field-name="<?php echo sanitize_html_class( $tag->name ); ?>"
					class="clear-button"><?php _e( 'Clear', 'ultimate-addons-cf7' ); ?></button>
			</div>

		</span>

		<?php
		$signature_buffer = ob_get_clean();

		return $signature_buffer;

	}

	/** Signature Tag Generator */

	public function uacf7_signature_tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_signature',
			__( 'Signature', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_signature' ],
			array( 'version' => '2' )
		);

	}

	public static function tg_pane_signature( $contact_form, $options ) {

		$field_types = array(
			'uacf7_signature' => array(
				'display_name' => __( 'Signature', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate a digital signature.', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		?>

		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_signature']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_signature']['description'],
				array(
					'a' => array( 'href' => true ),
					'strong' => array(),
				),
				array( 'http', 'https' )
			);

			echo $description;
			?></p>

            <div class="uacf7-doc-notice">
				<?php echo sprintf(
					__( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-signature-addon/" target="_blank">Digital Signature</a>'
				); ?>
			</div>
			
		</header>

		<div class="control-box uacf7-control-box">
			
			<?php

                $tgg->print( 'field_type', array(
                    'with_required' => true,
                    'select_options' => array(
                        'uacf7_signature' => $field_types['uacf7_signature']['display_name'],
                    ),
                ) );

                $tgg->print( 'field_name' );
                $tgg->print( 'class_attr' );

            ?>
		</div>
		
		<footer class="insert-box">
            <?php
            $tgg->print( 'insert_box_content' );

            $tgg->print( 'mail_tag_tip' );
            ?>
        </footer>

		<?php
	}



	/** Validation Callback */
	public function uacf7_signature_validation_filter( $result, $tag ) {
		$name = $tag->name;
		$empty = ! isset( $_FILES[ $name ]['name'] ) || empty( $_FILES[ $name ]['name'] ) && '0' !== $_FILES[ $name ]['name'];

		if ( $tag->is_required() and $empty ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

}

new UACF7_SIGNATURE;