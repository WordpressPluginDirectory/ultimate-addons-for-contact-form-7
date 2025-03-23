<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_COUNTRY_DROPDOWN {



	/*
	 * Construct function
	 */
	public function __construct() {

		add_action( 'wpcf7_init', array( $this, 'add_shortcodes' ) );

		add_action( 'admin_init', array( $this, 'tag_generator' ) );

		add_filter( 'wpcf7_validate_uacf7_country_dropdown', array( $this, 'wpcf7_country_dropdown_validation_filter' ), 10, 2 );

		add_filter( 'wpcf7_validate_uacf7_country_dropdown*', array( $this, 'wpcf7_country_dropdown_validation_filter' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_script' ) );


		/** Dynamic Selection JS Validation */
		// add_action( 'wp_ajax_uacf7_dynamic_selection', [$this, 'uacf7_dynamic_selection'] );
		// add_action( 'wp_ajax_nopriv_uacf7_dynamic_selection', [$this, 'uacf7_dynamic_selection'] );
	}

	public function wp_enqueue_script() {

		wp_enqueue_style( 'uacf7-country-select-main', UACF7_ADDONS . '/country-dropdown/assets/css/countrySelect.min.css' );
		wp_enqueue_style( 'uacf7-country-select-style', UACF7_ADDONS . '/country-dropdown/assets/css/style.css' );

		wp_enqueue_script( 'uacf7-country-select-library', UACF7_ADDONS . '/country-dropdown/assets/js/countrySelect.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'uacf7-country-select-script', UACF7_ADDONS . '/country-dropdown/assets/js/script.js', array( 'jquery', 'uacf7-country-select-library' ), null, true );
	}

	/*
	 * Form tag
	 */
	public function add_shortcodes() {

		wpcf7_add_form_tag( array( 'uacf7_country_dropdown', 'uacf7_country_dropdown*' ),
			array( $this, 'tag_handler_callback' ), array( 'name-attr' => true ) );
	}

	public function tag_handler_callback( $tag ) {

		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$ds_country = $tag->has_option( 'ds_country' );
		if ( $ds_country ) {
			$atts['ds_country'] = 'true';
			$class .= ' uacf7_country_dropdown_with_flag';
			$class .= ' uacf7_country_api';
		} else {
			$class .= ' uacf7_country_dropdown_with_flag';
		}
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$atts['name'] = $tag->name;

		/** Condition for Dynamic Selection (API Based Country, States, Cities) */
		/** Auto Complete */
		$country_auto_complete = $tag->has_option( 'country_auto_complete' );


		if ( $country_auto_complete ) {
			$atts['country_auto_complete'] = 'true';
		}
		$size = $tag->get_option( 'size', 'int', true );

		if ( $size ) {
			$atts['size'] = $size;
		}


		$country_atts = apply_filters( 'uacf7_get_country_attr', $atts, $tag );
		$atts = wpcf7_format_atts( $country_atts );

		ob_start(); ?>
		<select <?php echo $atts; ?> id="uacf7_country_api">
			<option value="">Select a Country</option>
		</select>
		<?php
		$api_country = ob_get_clean();
		ob_start();
		?>

		<?php if ( $ds_country ) { ?>
			<span id="uacf7_country_select" class="wpcf7-form-control-wrap  <?php echo sanitize_html_class( $tag->name ); ?>">

				<?php //echo apply_filters( 'uacf7_api_based_country_filter', $api_country, $atts ); ?>

				<input id="uacf7_countries_<?php echo esc_attr( $tag->name ); ?>" type="text" <?php echo $atts; ?>>

				<span><?php echo $validation_error; ?> </span>

				<div style="display:none;">
					<input type="hidden" id="uacf7_countries_<?php echo esc_attr( $tag->name ); ?>_code" data-countrycodeinput="1" readonly="readonly" placeholder="Selected country code will appear here" />
				</div>

			</span>
		<?php } else { ?>

			<span id="uacf7_country_select" class="wpcf7-form-control-wrap  <?php echo sanitize_html_class( $tag->name ); ?>">

				<input id="uacf7_countries_<?php echo esc_attr( $tag->name ); ?>" type="text" <?php echo $atts; ?>>

				<span><?php echo $validation_error; ?> </span>

				<div style="display:none;">
					<input type="hidden" id="uacf7_countries_<?php echo esc_attr( $tag->name ); ?>_code" data-countrycodeinput="1"
						readonly="readonly" placeholder="Selected country code will appear here" />
				</div>

			</span>
		<?php }
		$countries = ob_get_clean();
		return $countries;
	}


	public function wpcf7_country_dropdown_validation_filter( $result, $tag ) {
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
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;

		wpcf7_add_tag_generator( 'uacf7_country_dropdown',
			__( 'Country Dropdown', 'ultimate-addons-cf7' ),
			'uacf7-tg-pane-country-dropdown',
			array( $this, 'tg_pane_country_dropdown' ),
			array( 'version' => '2' )
		);

	}

	static function tg_pane_country_dropdown( $contact_form, $args ) {
		$args = wp_parse_args( $args, array() );
		$uacf7_field_type = 'uacf7_country_dropdown';
		$tgg = new WPCF7_TagGeneratorGenerator( $args['content'] );
		?>
		<header class="description-box">
			<h3>
				<?php echo esc_html( 'Country Dropdown' ); ?>
			</h3>

			<div class="uacf7-doc-notice">
				<?php echo sprintf(
					__( 'Confused? Check our Documentation on  %1s and %2s.', 'ultimate-addons-cf7' ),
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-country-dropdown-with-flag/" target="_blank">Country Dropdown</a>', '<a href="https://themefic.com/docs/uacf7/pro-addons/contact-form-7-autocomplete/" target="_blank">IP Geo Fields (Autocomplete)</a>'
				); ?>
			</div>

			<p class="uacf7-doc-notice uacf7-guide">
				<?php echo sprintf(
					__( 'Need  autocomplete feature for country, city, state, and zip code fields based on the user IP address? Try Our Pro addon %1s.', 'ultimate-addons-cf7' ),
					'<strong><a target="_blank" href="https://cf7addons.com/preview/contact-form-7-autocomplete/">IP Geolocation</a></strong>'
				); ?>

			</p>
		</header>
		<div class="control-box">
			<?php

			$tgg->print( 'field_type', array(
				'select_options' => array(
					'uacf7_country_dropdown' => 'Country Dropdown',
				),
				'with_required' => true,
			) );

			$tgg->print( 'field_name' );

			$tgg->print( 'class_attr' );
			?>

			<fieldset class="uacf7-tag-wraper">
				<?php ob_start(); ?>
				<legend>
					<?php echo esc_html( __( 'Auto complete', 'ultimate-addons-cf7' ) ); ?>
					<a style="color:red" target="_blank" href="https://cf7addons.com/">(Pro)</a>
				</legend>
			
				<input disabled type="checkbox" data-tag-part="option" data-tag-option="country_auto_complete"/>
				<?php echo esc_html( __( "Autocomplete country using user's network IP.", "ultimate-addons-cf7" ) ); ?>
				<?php
				$autocomplete_html = ob_get_clean();

				/*
				 * Tag generator field: auto complete
				 */

				echo apply_filters( 'uacf7_tag_generator_country_autocomplete_field', $autocomplete_html );
				?>
			</fieldset>

			<fieldset class="uacf7-tag-wraper">
				<?php ob_start(); ?>
				<legend>
					<?php echo esc_html( __( 'Dynamic Selection', 'ultimate-addons-cf7' ) ); ?>
					<a style="color:red" target="_blank" href="https://cf7addons.com/">(Pro)</a>
				</legend>
				<input disabled type="checkbox" class="option" data-tag-part="option" data-tag-option="ds_country"/>
				<?php echo esc_html( __( "Dynamically Populate Countries, States, and Cities", "ultimate-addons-cf7" ) ); ?>

				<?php
				$dynamic_selection = ob_get_clean();
				/*
				 * Tag generator field: Dynamic Selection
				 */
				echo apply_filters( 'uacf7_tag_generator_dynamic_selection', $dynamic_selection );
				?>
			</fieldset>

			<!-- Dynamic Selection Starts-->
			<fieldset class="uacf7-tag-wraper">
				<?php ob_start(); ?>

				<legend>
					<?php echo esc_html( __( 'Show Specific Countries', 'ultimate-addons-cf7' ) ); ?>
					<a style="color:red" target="_blank" href="https://cf7addons.com/">(Pro)</a>
				</legend>

				<textarea class="values" name="" id="tag-generator-panel-product-id" cols="30" rows="10" disabled></textarea>

				<br>
				<?php echo _e( ' One ID per line. ', 'ultimate-addons-cf7' ) ?>
				<?php
				$default_country = ob_get_clean();
				/*
				 * Tag generator field: auto complete
				 */
				echo apply_filters( 'uacf7_tag_generator_default_country_field', $default_country );
				 
				?>
			</fieldset>
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
new UACF7_COUNTRY_DROPDOWN();
