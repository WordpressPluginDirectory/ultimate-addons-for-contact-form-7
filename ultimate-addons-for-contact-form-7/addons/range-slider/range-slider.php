<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * range slider class
 */
class UACF7_range_Slider {

	/**
	 * Form ID to be used globally within the class.
	 *
	 * @var int|null
	 */
	private $form_id;

	/**
	 * Constructor Function
	 */
	public function __construct() {
		add_action( 'wpcf7_init', array( $this, 'add_shortcodes' ) );
		add_action( 'admin_init', array( $this, 'tag_generator' ) );
		add_action( 'wpcf7_contact_form_properties', array( $this, 'uacf7_contact_form_properties' ), 5, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_slider_scripts' ) );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_range_slider' ), 23, 2 );
	}



	public function uacf7_post_meta_options_range_slider( $value, $post_id ) {

		$range_silder = apply_filters( 'uacf7_post_meta_options_range_slider_pro', $data = array(
			'title' => __( 'Range Slider', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-sliders',
			'checked_field' => 'uacf7_range_selection_color',
			'fields' => array(
				'uacf7_range_slider_heading' => array(
					'id' => 'uacf7_range_slider_heading',
					'type' => 'heading',
					'label' => __( 'Range Slider Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Add beautiful Range slider fields to Contact Form 7, with multiple preview layouts available on Pro. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/contact-form-7-range-slider/" target="_blank" rel="noopener">Example</a>'
					)
				),
				'range_slider_docs' => array(
					'id' => 'range_slider_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s and %2s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-range-slider/" target="_blank" rel="noopener">Range Slider</a>',
						'<a href="https://themefic.com/docs/uacf7/pro-addons/contact-form-7-range-slider-pro/" target="_blank" rel="noopener">Range Slider (Pro)</a>'
					)
				),
				'uacf7_range_slider_form_options_heading' => array(
					'id' => 'uacf7_range_slider_form_options_heading',
					'type' => 'heading',
					'label' => __( 'Range Slider Option ', 'ultimate-addons-cf7' ),
				),
				'uacf7_range_selection_color' => array(
					'id' => 'uacf7_range_selection_color',
					'type' => 'color',
					'label' => __( ' Slider Selection Color', 'ultimate-addons-cf7' ),
					'field_width' => 50

				),
				'uacf7_range_handle_color' => array(
					'id' => 'uacf7_range_handle_color',
					'type' => 'color',
					'label' => __( ' Slider Handle Color ', 'ultimate-addons-cf7' ),
					'field_width' => 50

				),
				'uacf7_range_handle_width' => array(
					'id' => 'uacf7_range_handle_width',
					'type' => 'number',
					'label' => __( ' Slider Handle Width', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. 5 (Do not add px or em)', 'ultimate-addons-cf7' ),
					'field_width' => 50

				),
				'uacf7_range_handle_height' => array(
					'id' => 'uacf7_range_handle_height',
					'type' => 'number',
					'label' => __( ' Slider Handle Height', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. 10 (Do not add px or em)', 'ultimate-addons-cf7' ),
					'field_width' => 50

				),

				'uacf7_range_handle_border_radius' => array(
					'id' => 'uacf7_range_handle_border_radius',
					'type' => 'number',
					'label' => __( ' Handle Border Radius', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. 8 (Do not add px or em)', 'ultimate-addons-cf7' ),
					'field_width' => 50

				),
				'uacf7_range_slider_height' => array(
					'id' => 'uacf7_range_slider_height',
					'type' => 'number',
					'label' => __( ' Slider Height (px)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. 20 (Do not add px or em)', 'ultimate-addons-cf7' ),
					'field_width' => 50

				),

			)


		), $post_id );

		$value['range_slider'] = $range_silder;
		return $value;
	}

	/**
	 * add form tag
	 */
	public function add_shortcodes() {
		wpcf7_add_form_tag(
			array( 'uacf7_range_slider', 'uacf7_range_slider*' ),
			array( $this, 'slider_tag_handler_callback' ),
			array( 'name-attr' => true )
		);
	}

	/**
	 * Slider tag callback
	 */
	public function slider_tag_handler_callback( $tag ) {

		$validation_error = wpcf7_get_validation_error( $tag->name );
		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();
		$class .= ' uacf7-range-slider';
		$atts['class'] = $class;

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
		$atts = wpcf7_format_atts( $atts );

		$show_value = ! empty( $tag->get_option( 'show_value', '', true ) ) ? $tag->get_option( 'show_value', '', true ) : 'on';
		$handle = ! empty( $tag->get_option( 'handle', '', true ) ) ? $tag->get_option( 'handle', '', true ) : '1';
		if ( class_exists( 'UACF7_RANGE_SLIDER_PRO' ) ) {
			$label = ! empty( $tag->get_option( 'label', '', true ) ) ? $tag->get_option( 'label', '', true ) : '';
			$separator = ! empty( $tag->get_option( 'separator', '', true ) ) ? $tag->get_option( 'separator', '', true ) : '-';
			$min_label = ! empty( $tag->get_option( 'min_label', '', true ) ) ? $tag->get_option( 'min_label', '', true ) : 'Min : ';
			$max_label = ! empty( $tag->get_option( 'max_label', '', true ) ) ? $tag->get_option( 'max_label', '', true ) : 'Max : ';
		} else {
			$label = '';
			$separator = '-';
			$min_label = 'Min : ';
			$max_label = 'Max : ';
		}

		$min = ! empty( $tag->get_option( 'min', '', true ) ) ? $tag->get_option( 'min', '', true ) : 0;
		$max = ! empty( $tag->get_option( 'max', '', true ) ) ? $tag->get_option( 'max', '', true ) : 100;
		$default = ! empty( $tag->get_option( 'default', '', true ) ) ? $tag->get_option( 'default', '', true ) : 100;
		$step = ! empty( $tag->get_option( 'step', '', true ) ) ? $tag->get_option( 'step', '', true ) : 1;
		$steps = '0';
		for ( $x = $step; $x <= $max; $x += $step ) {
			$steps .= ',' . $x . '';
		}

		// return array for range style as $values[0]
		if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
		}
		$values = $tag->values;
		$newValue = ( esc_html( $default ) - esc_html( $min ) ) * 100 / ( esc_html( $max ) - esc_html( $min ) );

		ob_start();

		if ( $handle == 1 ) {
			echo '<div class="' . esc_attr( $tag->name ) . '">';
			if ( $show_value == 'on' ) {
				?>
				<label class="uacf7-slider-label"> (
					<span class="min-max-label"><?php echo esc_html( $min_label ); ?> </span>
					<span class="range-min">
						<?php echo esc_html( $min ); ?>
					</span>
					<span class="range-label"><?php echo esc_html( $label ); ?> </span>
					<span class="range-separator"><?php echo esc_html( $separator ); ?> </span>
					<span class="min-max-label"><?php echo esc_html( $max_label ); ?> </span>
					<span class="range-min">
						<?php echo esc_html( $max ) ?>
					</span>
					<span class="range-label"> <?php echo esc_html( $label ); ?></span>
					)
				</label>
				<?php
			}
			?>
			<span class="<?php echo esc_attr( $tag->name ) . '-value'; ?> uacf7-value"><?php echo esc_html( $default ) ?></span>
			<span class="wpcf7-form-control-wrap uacf7-slidecontainer uacf7-slider-handle"
				data-separator="<?php echo esc_attr( $separator ); ?>" data-handle="<?php echo esc_attr( $handle ); ?>"
				data-min="<?php echo esc_attr( $min ); ?>" data-max="<?php echo esc_attr( $max ); ?>"
				data-default="<?php echo esc_attr( $default ); ?>">
				<input name="<?php echo esc_attr( $tag->name ); ?>" type="range" min="<?php echo esc_attr( $min ); ?>"
					max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>"
					value="<?php echo esc_attr( $default ); ?>" class="uacf7-slider uacf7-range">
			</span>
			<span>
				<?php echo $validation_error; ?>
			</span>
			</div>
			<?php
		} elseif ( $handle == 2 ) {
			echo '<div class="' . esc_attr( $tag->name ) . '">';
			if ( $show_value == 'on' ) {
				?>
				<label class="uacf7-slider-label"> (
					<span class="min-max-label"><?php echo esc_html( $min_label ); ?> </span> <span class="range-min">
						<?php echo esc_html( $min ); ?></span>
					<span class="range-label"><?php echo esc_html( $label ); ?> </span>
					<span class="range-separator"><?php echo esc_html( $separator ); ?> </span>
					<span class="min-max-label"><?php echo esc_html( $max_label ); ?> </span> <span class="range-min">
						<?php echo esc_html( $max ) ?></span>
					<span class="range-label"> <?php echo esc_html( $label ); ?></span>
					)</label>
				<?php
			}
			?>
			<div class="multistep">
				<span class="wpcf7-form-control-wrap"><span
						class="uacf7-amount"><?php echo esc_attr( $min . " $label " . " $separator " . $max . " $label " ); ?></span>
					<span class="uacf7-slider-handle" data-step="<?php echo esc_attr( $step ); ?>"
						data-separator="<?php echo esc_attr( $separator ); ?>" data-label="<?php echo esc_attr( $label ); ?>"
						data-handle="<?php echo esc_attr( $handle ); ?>" data-min="<?php echo esc_attr( $min ); ?>"
						data-max="<?php echo esc_attr( $max ); ?>" data-default="<?php echo esc_attr( $default ); ?>">
						<input step="<?php echo esc_attr( $step ); ?>" name="<?php echo esc_attr( $tag->name ) ?>" type="hidden"
							id="uacf7-amount" class="uacf7-slide_amount" readonly>
						<div id="uacf7-slider-range" class="multistep_slide"></div>
					</span>
					<!-- </span> -->
			</div>

			<span>
				<?php echo $validation_error; ?>
			</span>
			</div>
			<?php
		}

		$default_layout = ob_get_clean();
		return apply_filters( 'uacf7_range_slider_style_pro_feature', $default_layout, $tag );

	}

	/**
	 * Tag Generator
	 */
	public function tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_range_slider',
			__( 'Range Slider', 'ultimate-addons-cf7' ),
			[ $this, 'tg_panel_range_slider' ],
			array( 'version' => '2' )
		);

	}

	static function tg_panel_range_slider( $cf, $options ) {

		$field_types = array(
			'uacf7_range_slider' => array(
				'display_name' => __( 'Range Slider', 'ultimate-addons-cf7' ),
				'heading' => __( 'Range Slider', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );
		?>
		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_range_slider']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_range_slider']['description'],
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
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-range-slider/" target="_blank">Range Slider</a>'
				); ?>
			</div>
		</header>
		<div class="control-box uacf7-control-box">

			<?php

			$tgg->print( 'field_type', array(
				'with_required' => true,
				'select_options' => array(
					'uacf7_range_slider' => $field_types['uacf7_range_slider']['display_name'],
				),
			) );

			$tgg->print( 'field_name' );

			?>
			<fieldset>
				<legend>
					<?php echo esc_html__( 'Show Values', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="radio" data-tag-part="option" data-tag-option="show_value:" name="show_value" id="show_value_on"
					value="on" />
				<?php echo esc_html( 'On', 'ultimate-addons-cf7' ); ?></label>
				<input type="radio" data-tag-part="option" data-tag-option="show_value:" name="show_value" id="show_value_off"
					value="off" />
				<?php echo esc_html( 'Off', 'ultimate-addons-cf7' ); ?></label></table>

			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Slider Type', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="radio" name="handle" data-tag-part="option" data-tag-option="handle:" id="single_handle"
					value="1" />
				<?php echo esc_html( 'Single Handle', 'ultimate-addons-cf7' ); ?>

				<input type="radio" name="handle" data-tag-part="option" data-tag-option="handle:" id="double_handle"
					value="2" />
				<?php echo esc_html( 'Double Handle', 'ultimate-addons-cf7' ); ?>

			</fieldset>

			<!-- Pro Version  -->
			<?php ob_start() ?>
			<fieldset>
				<legend>
					<?php echo esc_html__( 'Range Slider Style', 'ultimate-addons-cf7' ); ?>
				</legend>
				<select name="values" disabled data-tag-part="value" id="tag-generator-panel-range-style">
					<option value="default">Default</option>
				</select>
				<a href="https://cf7addons.com/preview/range-slider/pro" style="color:red">(Pro)</a>
			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Range Label', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="text" name="label" class="tg-range_label oneline option" id="tag-generator-panel-text-min"
					placeholder="" disabled />
				<a href="https://cf7addons.com/preview/range-slider/pro" style="color:red">(Pro)</a>

			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Range Separator', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="text" name="separator" class="tg-range_separator oneline option" id="tag-generator-panel-text-min"
					placeholder="-" disabled />
				<a href="https://cf7addons.com/preview/range-slider/pro" style="color:red">(Pro)</a>

			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Minimum Label', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="text" name="min_label" class="tg-min_label oneline option" id="tag-generator-panel-text-min"
					placeholder="Min" disabled />
				<a href="https://cf7addons.com/preview/range-slider/pro" style="color:red">(Pro)</a>

			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Maximum Label', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="text" name="max_label" class="tg-max_label oneline option" id="tag-generator-panel-text-min"
					placeholder="Max" disabled />

				<a href="https://cf7addons.com/preview/range-slider/pro" style="color:red">(Pro)</a>

				<div class="uacf7-doc-notice uacf7-guide">
					<?php echo esc_attr( __( "You can configure the slider's styles using the 'Range Slider' tab located below.", 'ultimate-addons-cf7' ) ); ?>
				</div>
			</fieldset>
			<?php
			$range_style = ob_get_clean();
			echo apply_filters( 'uacf7_range_slider_style_field', $range_style );
			?>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Minimum range', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="number" data-tag-part="option" data-tag-option="min:" name="min" id="tag-generator-panel-text-min"
					placeholder="15" />
			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Maximum range', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="number" data-tag-part="option" data-tag-option="max:" name="max" id="tag-generator-panel-text-max"
					placeholder="100" />
			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Default Value', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="number" data-tag-part="option" data-tag-option="default:" name="default"
					id="tag-generator-panel-text-default" placeholder="50" />
			</fieldset>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Range steps', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="number" data-tag-part="option" data-tag-option="step:" name="step"
					id="tag-generator-panel-text-step" placeholder="1" /></td>
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


	/**
	 * Contact Form Properties
	 */
	public function uacf7_properties( $properties, $cf ) {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$form = $properties['form'];

			ob_start();
			$range_slider = uacf7_get_form_option( $cf->id(), 'range_slider' );
			$selection_color = isset( $range_slider['uacf7_range_selection_color'] ) && ! empty( $range_slider['uacf7_range_selection_color'] ) ? $range_slider['uacf7_range_selection_color'] : "#1e90ff";
			$handle_width = isset( $range_slider['uacf7_range_handle_width'] ) && ! empty( $range_slider['uacf7_range_handle_width'] ) ? $range_slider['uacf7_range_handle_width'] : '24';
			$handle_height = isset( $range_slider['uacf7_range_handle_height'] ) && ! empty( $range_slider['uacf7_range_handle_height'] ) ? $range_slider['uacf7_range_handle_height'] : '24';
			$handle_border_radius = isset( $range_slider['uacf7_range_handle_border_radius'] ) && ! empty( $range_slider['uacf7_range_handle_border_radius'] ) ? $range_slider['uacf7_range_handle_border_radius'] : '24';
			$handle_color = isset( $range_slider['uacf7_range_handle_color'] ) && ! empty( $range_slider['uacf7_range_handle_color'] ) ? $range_slider['uacf7_range_handle_color'] : '#3498db';
			$range_slider_height = isset( $range_slider['uacf7_range_slider_height'] ) && ! empty( $range_slider['uacf7_range_slider_height'] ) ? $range_slider['uacf7_range_slider_height'] : 9;
			$handle_dynamic_position = ( intval( $handle_height ) / 2 - intval( $range_slider_height ) / 2 ) + 1;

			?>
			<style>
				:root {
					--uacf7-slider-Selection-Color:
						<?php echo esc_attr( $selection_color ); ?>
					;
					--uacf7-slider-handle-color:
						<?php echo esc_attr( $handle_color ); ?>
					;
					--uacf7-slider-handle-width:
						<?php echo esc_attr( $handle_width ) . "px"; ?>
					;
					--uacf7-slider-handle-height:
						<?php echo esc_attr( $handle_height ) . "px"; ?>
					;
					--uacf7-slider-handle-border-radius:
						<?php echo esc_attr( $handle_border_radius ) . "px"; ?>
					;
					--uacf7-slider-range-slider-height:
						<?php echo esc_attr( $range_slider_height ) . "px"; ?>
					;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .ui-slider-horizontal .ui-slider-range {
					background-color:
						<?php echo esc_attr( $selection_color ); ?>
					;
					height:
						<?php echo esc_attr( $range_slider_height ) . "px"; ?>
					;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .ui-widget.ui-widget-content {
					height:
						<?php echo esc_attr( $range_slider_height ) . "px"; ?>
					;
					border: 1px solid
						<?php echo esc_attr( $selection_color ); ?>
					;
					background-color: #EEE;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .ui-state-default,
				.ui-widget-content .ui-state-default {
					background-color:
						<?php echo esc_attr( $handle_color ); ?>
					;
					width:
						<?php echo esc_attr( $handle_width ) . "px"; ?>
					;
					height:
						<?php echo esc_attr( $handle_height ) . "px"; ?>
					;
					border-radius:
						<?php echo esc_attr( $handle_border_radius ) . "px"; ?>
					;
					cursor: pointer;
					border: none !important;
					top: -8px;
					position: absolute;

				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .ui-slider-horizontal .ui-slider-handle {
					top: -<?php echo esc_attr( $handle_dynamic_position ) ?>px;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> input[type=range] {
					background-color:
						<?php echo esc_attr( $selection_color ); ?>
					;
					height:
						<?php echo esc_attr( $range_slider_height ) . "px"; ?>
					;
					border-radius: 5px;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .ui-slider-horizontal {
					height:
						<?php echo esc_attr( $range_slider_height ) . "px"; ?>
					;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .uacf7-slider::-webkit-slider-thumb {
					-webkit-appearance: none;
					appearance: none;
					width:
						<?php echo esc_attr( $handle_width ) . "px"; ?>
					;
					height:
						<?php echo esc_attr( $handle_height ) . "px"; ?>
					;
					background:
						<?php echo esc_attr( $handle_color ); ?>
					;
					border-radius:
						<?php echo esc_attr( $handle_border_radius ); ?>
						px;
					cursor: pointer;
				}

				.uacf7-form-<?php echo esc_attr( $cf->id() ); ?> .uacf7-slider::-moz-range-thumb {
					width:
						<?php echo esc_attr( $handle_width ) . "px"; ?>
					;
					height:
						<?php echo esc_attr( $handle_height ) . "px"; ?>
					;
					background:
						<?php echo esc_attr( $handle_color ); ?>
					;
					border-radius:
						<?php echo esc_attr( $handle_border_radius ); ?>
						px;
					cursor: pointer;
				}
			</style>

			<?php

			echo '<div class="uacf7-form-' . $cf->id() . '">' . $form . '</div>';
			$properties['form'] = ob_get_clean();

		}
		return $properties;
	}

	public function uacf7_contact_form_properties( $properties, $cf ) {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			wp_register_style( 'uacf7-rangeSlider-dynamic', UACF7_URL . 'addons/range-slider/css/uacf7-range.css', array(), null );

			$form_id = $cf->id();
			$range_slider = uacf7_get_form_option( $form_id, 'range_slider' );

			// Define styling values
			$range_slider = uacf7_get_form_option( $cf->id(), 'range_slider' );
			$selection_color = isset( $range_slider['uacf7_range_selection_color'] ) && ! empty( $range_slider['uacf7_range_selection_color'] ) ? $range_slider['uacf7_range_selection_color'] : "#1e90ff";
			$handle_width = isset( $range_slider['uacf7_range_handle_width'] ) && ! empty( $range_slider['uacf7_range_handle_width'] ) ? $range_slider['uacf7_range_handle_width'] : '24';
			$handle_height = isset( $range_slider['uacf7_range_handle_height'] ) && ! empty( $range_slider['uacf7_range_handle_height'] ) ? $range_slider['uacf7_range_handle_height'] : '24';
			$handle_border_radius = isset( $range_slider['uacf7_range_handle_border_radius'] ) && ! empty( $range_slider['uacf7_range_handle_border_radius'] ) ? $range_slider['uacf7_range_handle_border_radius'] : '24';
			$handle_color = isset( $range_slider['uacf7_range_handle_color'] ) && ! empty( $range_slider['uacf7_range_handle_color'] ) ? $range_slider['uacf7_range_handle_color'] : '#3498db';
			$range_slider_height = isset( $range_slider['uacf7_range_slider_height'] ) && ! empty( $range_slider['uacf7_range_slider_height'] ) ? $range_slider['uacf7_range_slider_height'] : 9;
			$handle_dynamic_position = ( intval( $handle_height ) / 2 - intval( $range_slider_height ) / 2 ) + 1;

			// Inline CSS specific to this form ID

			$css = "
					:root {
					--uacf7-slider-Selection-Color: {$selection_color};
					--uacf7-slider-handle-color: {$handle_color};
					--uacf7-slider-handle-width: {$handle_width}px;
					--uacf7-slider-handle-height: {$handle_height}px;
					--uacf7-slider-handle-border-radius:{$handle_border_radius}px;
					--uacf7-slider-range-slider-height: {$range_slider_height}px;
					}
					.uacf7-form-{$form_id} .ui-slider-horizontal .ui-slider-range {
						background-color: {$selection_color};
						height: {$range_slider_height}px;
					}
					.uacf7-form-{$form_id} .ui-widget.ui-widget-content {
						height: {$range_slider_height}px;
						border: 1px solid {$selection_color};
						background-color: #EEE;
					}
					.uacf7-form-{$form_id} .ui-state-default,
					.ui-widget-content .ui-state-default {
						background-color: {$handle_color};
						width: {$handle_width}px;
						height: {$handle_height}px;
						border-radius: {$handle_border_radius}px;
						cursor: pointer;
						border: none !important;
						top: -8px;
						position: absolute;
					}
					.uacf7-form-{$form_id} .ui-slider-horizontal .ui-slider-handle {
						top: -{$handle_dynamic_position}px;
					}
					.uacf7-form-{$form_id} input[type=range] {
						background-color: {$selection_color};
						height: {$range_slider_height}px;
						border-radius: 5px;
					}
					.uacf7-form-{$form_id} .ui-slider-horizontal {
						height: {$range_slider_height}px;
					}
					.uacf7-form-{$form_id} .uacf7-slider::-webkit-slider-thumb {
						width: {$handle_width}px;
						height: {$handle_height}px;
						background: {$handle_color};
						border-radius: {$handle_border_radius}px;
						cursor: pointer;
					}
					.uacf7-form-{$form_id} .uacf7-slider::-moz-range-thumb {
						width: {$handle_width}px;
						height: {$handle_height}px;
						background: {$handle_color};
						border-radius: {$handle_border_radius}px;
						cursor: pointer;
					}";

			// Ensure the stylesheet is enqueued
			wp_add_inline_style( 'uacf7-rangeSlider-dynamic', $css );
			wp_enqueue_style( 'uacf7-rangeSlider-dynamic' ); 

		}

		return $properties;
	}

	/**
	 * Enqueue Slider scripts
	 */
	public function enqueue_slider_scripts() {
		wp_enqueue_script( 'uacf7-range-slider', UACF7_URL . 'addons/range-slider/js/range-slider.js', array( 'jquery', 'jquery-ui' ), false, true );
		wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
		wp_enqueue_style( 'range-slider-style', UACF7_URL . 'addons/range-slider/css/style.css' );

		wp_register_script( 'jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array( 'jquery' ), false, true );
		wp_register_script( 'touch-punch', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'touch-punch' );

	}

}

new UACF7_range_Slider;