<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_DYNAMIC_TEXT {

	/*
	 * Construct function
	 */
	public function __construct() {
		add_action( 'wpcf7_init', array( $this, 'add_shortcodes' ) );

		add_action( 'admin_init', array( $this, 'tag_generator' ) );

		add_filter( 'wpcf7_validate_uacf7_dynamic_text', array( $this, 'uacf7_dynamic_text_validation_filter' ), 10, 2 );

		add_filter( 'wpcf7_validate_uacf7_dynamic_text*', array( $this, 'uacf7_dynamic_text_validation_filter' ), 10, 2 );


		//Require Shortcode
		require_once( 'inc/shortcode.php' );

	}

	/*
	 * Form tag
	 */
	public function add_shortcodes() {

		wpcf7_add_form_tag( array( 'uacf7_dynamic_text', 'uacf7_dynamic_text*' ),
			array( $this, 'uacf7_dynamic_text_tag_handler_callback' ), array( 'name-attr' => true ) );
	}

	/*
	 * Form tag shortcode
	 */
	public function uacf7_dynamic_text_tag_handler_callback( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}
		// beaf_print_r($tag, $tag->name);
		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$atts['name'] = $tag->name;

		// input size
		$size = $tag->get_option( 'size', 'int', true );
		if ( $size ) {
			$atts['size'] = $size;
		} else {
			$atts['size'] = 40;
		}

		// Visibility
		$visibility = $tag->get_option( 'visibility', '', true );
		if ( $visibility == 'show' ) {
			$atts['type'] = 'text';
		} elseif ( $visibility == 'disabled' ) {
			$atts['type'] = 'text';
			$atts['disabled'] = 'disabled';
		} elseif ( $visibility == 'hidden' ) {
			$atts['type'] = 'hidden';
		}


		$values = $tag->values;
		$key = $tag->get_option( 'key', '', true );

		// Short Code
		$shortcode = '';
		if ( ! empty( $values ) ) {
			$shortcode = do_shortcode( '[' . esc_attr( $values[0] ) . ' attr="' . esc_attr( $key ) . '"]' );
		}
		$atts['value'] = esc_attr( $shortcode );

		$atts = wpcf7_format_atts( $atts );
		ob_start();

		?>
		<span class="wpcf7-form-control-wrap <?php echo sanitize_html_class( $tag->name ); ?>"
			data-name="<?php echo sanitize_html_class( $tag->name ); ?>">

			<input id="uacf7_<?php echo esc_attr( $tag->name ); ?>" <?php echo $atts; ?>>
			<span><?php echo $validation_error; ?></span>
		</span>
		<?php

		$countries = ob_get_clean();

		return $countries;
	}


	/*
	 * Form tag Validation 
	 */
	public function uacf7_dynamic_text_validation_filter( $result, $tag ) {
		$name = $tag->name;

		if ( isset( $_POST[ $name ] )
			and is_array( $_POST[ $name ] ) ) {
			foreach ( $_POST[ $name ] as $key => $value ) {
				if ( '' === $value ) {
					unset( $_POST[ $name ][ $key ] );
				}
			}
		}

		$empty = ! isset( $_POST[ $name ] ) || empty( $_POST[ $name ] ) && '0' !== $_POST[ $name ];

		if ( $tag->is_required() and $empty ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}


	/*
	 * Generate tag - conditional
	 */
	public function tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_dynamic_text',
			__( 'Dynamic Text', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_uacf7_dynamic_text' ],
			array( 'version' => '2' )
		);

	}


	static function tg_pane_uacf7_dynamic_text( $contact_form, $options ) {

		$field_types = array(
			'uacf7_dynamic_text' => array(
				'display_name' => __( 'Dynamic Text', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate a Dynamic Text.', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		?>
		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_dynamic_text']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_dynamic_text']['description'],
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
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-dynamic-text-extension/" target="_blank">Dynamic Text</a>'
				); ?>
			</div>

		</header>

		<div class="control-box uacf7-control-box">
			<?php

			$tgg->print( 'field_type', array(
				'with_required' => true,
				'select_options' => array(
					'uacf7_dynamic_text' => $field_types['uacf7_dynamic_text']['display_name'],
				),
			) );

			$tgg->print( 'field_name' );

			?>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Field Visibility', 'ultimate-addons-cf7' ); ?>
				</legend>

				<select data-tag-part="option" data-tag-option="visibility:">
					<option value="show">
						<?php echo esc_html__( 'Show', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="disabled">
						<?php echo esc_html__( 'Disabled', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="hidden">
						<?php echo esc_html__( 'Hidden', 'ultimate-addons-cf7' ); ?>
					</option>
				</select>

			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Choose Field', 'ultimate-addons-cf7' ); ?>
				</legend>
                
				<select id="uacf7-choose-field" data-tag-part="value">
					<option value="">
                        <?php echo esc_html__( 'Select', 'ultimate-addons-cf7' ); ?>
                    </option>
					<option value="UACF7_URL">
                        <?php echo esc_html__( 'Current URL', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="UACF7_URL part=host">
						<?php echo esc_html__('Current URL Host (Domain)', 'ultimate-addons-cf7'); ?>
					</option>
					<option value="UACF7_URL part=query">
						<?php echo esc_html__('Current URL Query String', 'ultimate-addons-cf7'); ?>
					</option>
					<option value="UACF7_URL part=path">
						<?php echo esc_html__('Current URL Path', 'ultimate-addons-cf7'); ?>
					</option>
					<option value="UACF7_URL_WITH_PERAMETERS">
						<?php echo esc_html__( 'Current URL with Perameters', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="UACF7_BLOGINFO">
						<?php echo esc_html__( 'Blog Info', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="UACF7_POSTINFO">
						<?php echo esc_html__( 'Current post info', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="UACF7_USERINFO">
						<?php echo esc_html__( 'Current User info', 'ultimate-addons-cf7' ); ?>
					</option>
					<option value="UACF7_CUSTOM_FIELDS">
						<?php echo esc_html__( 'Custom fields', 'ultimate-addons-cf7' ); ?>
					</option>
				</select>
			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Dynamic key', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="text" data-tag-part="option" data-tag-option="key:" placeholder="Dynamic key" >			
			</fieldset>

			<?php
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
}
new UACF7_DYNAMIC_TEXT();