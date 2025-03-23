<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_MULTISTEP {

	private $hidden_fields = array();

	/*
	 * Construct function
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
		add_action( 'admin_init', array( $this, 'tag_generator' ) );
		add_action( 'wp_ajax_check_fields_validation', array( $this, 'check_fields_validation' ) );
		add_action( 'wp_ajax_nopriv_check_fields_validation', array( $this, 'check_fields_validation' ) );
		wpcf7_add_form_tag( 'uacf7_step_start', array( $this, 'step_start_tag_handler' ), true );
		wpcf7_add_form_tag( 'uacf7_step_end', array( $this, 'step_end_tag_handler' ), false );
		wpcf7_add_form_tag( 'uacf7_multistep_progressbar', array( $this, 'uacf7_multistep_progressbar' ), true );
		add_filter( 'wpcf7_contact_form', array( $this, 'uacf7_properties' ), 12, 1 );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_multistep' ), 14, 2 );
		add_filter( 'uacf7_post_meta_options_multistep_pro', array( $this, 'uacf7_post_meta_options_multistep_pro' ), 10, 2 );
		add_filter( 'uacf7_multistep_steps_names', array( $this, 'uacf7_multistep_steps_names' ), 10, 2 );
		add_filter( 'uacf7_multistep_step_title', array( $this, 'uacf7_multistep_step_title' ), 10, 2 );
	}

	public function enqueue_script() {
		wp_enqueue_script( 'uacf7-multistep', UACF7_ADDONS . '/multistep/assets/js/multistep.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'uacf7-progressbar', UACF7_ADDONS . '/multistep/assets/js/progressbar.js', array( 'jquery' ), null, true );
		wp_enqueue_style( 'uacf7-multistep-style', UACF7_ADDONS . '/multistep/assets/css/multistep.css' );


		wp_localize_script( 'uacf7-multistep', 'uacf7_multistep_obj', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'uacf7-multistep' ) ) );
	}

	// Steps Name: uacf7_multistep_steps_names
	function uacf7_multistep_steps_names( $steps_name, $all_steps ) {
		$step_names = array();
		foreach ( $all_steps as $step ) {
			$step_names[] = ! empty( $step->name ) ? $step->name : '';
		}
		return $step_names;
	}

	// Steps Name: uacf7_multistep_steps_names
	function uacf7_multistep_step_title( $step_titles, $all_steps ) {
		$step_titles = array();
		foreach ( $all_steps as $step ) {
			$step_titles[] = ( is_array( $step->values ) && ! empty( $step->values ) ) ? $step->values[0] : '';
		}
		return $step_titles;
	}


	public function uacf7_post_meta_options_multistep( $value, $post_id ) {

		$multistep = apply_filters( 'uacf7_post_meta_options_multistep_pro', $data = array(
			'title' => __( 'Multi-step Form', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-stairs',
			'checked_field' => 'uacf7_multistep_is_multistep',
			'fields' => array(
				'placeholder_heading' => array(
					'id' => 'placeholder_heading',
					'type' => 'heading',
					'label' => __( 'Multi-step Form Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Create stunning multi-step forms with Contact Form 7. Ideal solution for long forms. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/contact-form-7-multi-step-forms/" target="_blank">Example</a>'
					)
				),
				'multistep_form_docs' => array(
					'id' => 'multistep_form_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s and %2s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-multi-step-forms/" target="_blank">Multi-step Form</a>',
						'<a href="https://themefic.com/docs/uacf7/pro-addons/contact-form-7-multi-step-form-pro/" target="_blank">Multi-step Form (Pro)</a>'
					)
				),
				'uacf7_multistep_is_multistep' => array(
					'id' => 'uacf7_multistep_is_multistep',
					'type' => 'switch',
					'label' => __( ' Enable Multistep', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 100,
				),
				'uacf7_multistep_form_options_heading' => array(
					'id' => 'uacf7_multistep_form_options_heading',
					'type' => 'heading',
					'label' => __( 'Multistep Option ', 'ultimate-addons-cf7' ),
				),
				'uacf7_enable_multistep_progressbar' => array(
					'id' => 'uacf7_enable_multistep_progressbar',
					'type' => 'switch',
					'label' => __( ' Enable Progressbar ', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 50,
				),
				'uacf7_enable_multistep_scroll' => array(
					'id' => 'uacf7_enable_multistep_scroll',
					'type' => 'switch',
					'label' => __( 'Enable Form Auto Scrolling ', 'ultimate-addons-cf7' ),
					'description' => __( 'Auto scroll to top after clicking the next button.', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'is_pro' => true,
					'field_width' => 50,
				),
				'uacf7_multistep_use_step_labels' => array(
					'id' => 'uacf7_multistep_use_step_labels',
					'type' => 'switch',
					'label' => __( 'Hide Progressbar Labels ', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 50,
				),
				'uacf7_progressbar_style' => array(
					'id' => 'uacf7_progressbar_style',
					'type' => 'imageselect',
					'label' => __( 'Choose Form Layout / Style', 'ultimate-addons-cf7' ),
					'description' => __( 'See live demo examples <a href="https://cf7addons.com/preview/contact-form-7-multi-step-forms/pro/" target="_blank">here</a>.', 'ultimate-addons-cf7' ),
					'multiple' => true,
					'inline' => true,
					'options' => array(
						'default' => array(
							'title' => 'Default',
							'url' => UACF7_ADDONS . "/multistep/assets/img/default.png",
						),
						'style-1' => array(
							'title' => 'Style 1',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-1.png",
						),
						'style-2' => array(
							'title' => 'Style 2',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-2.png",
							'is_pro' => true,
						),
						'style-3' => array(
							'title' => 'Style 3',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-3.png",
							'is_pro' => true,
						),
						'style-4' => array(
							'title' => 'Style 4',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-4.png",
							'is_pro' => true,
						),
						'style-5' => array(
							'title' => 'Style 5',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-5.png",
							'is_pro' => true,
						),
						'style-6' => array(
							'title' => 'Style 6',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-6.png",
							'is_pro' => true,
						),
						'style-7' => array(
							'title' => 'Style 7',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-7.png",
							'is_pro' => true,
						),
						'style-8' => array(
							'title' => 'Style 8',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-8.png",
							'is_pro' => true,
						),
						'style-9' => array(
							'title' => 'Style 9',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-9.png",
							'is_pro' => true,
						),
						'style-10' => array(
							'title' => 'Style 10',
							'url' => UACF7_ADDONS . "/multistep/assets/img/skin-10.png",
							'is_pro' => true,
						),
					),

				),

				'uacf7_progressbar_styler' => array(
					'id' => 'uacf7_progressbar_styler',
					'type' => 'heading',
					'label' => __( 'Progressbar Styler', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'All modifications in this section are applicable to the "Progressbar" of the form.', 'ultimate-addons-cf7' ),
				),

				'uacf7_multistep_step_height' => array(
					'id' => 'uacf7_multistep_step_height',
					'type' => 'select',
					'label' => __( 'Progressbar Height', 'ultimate-addons-cf7' ),
					// 'placeholder'     => __( 'width', 'ultimate-addons-cf7' ),  
					'options' => array(
						'default' => 'Default',
						'equal-height' => 'Equal height'
					),
					'dependency' => array(
						array( 'uacf7_progressbar_style', '!=', 'default' ),
						array( 'uacf7_progressbar_style', '!=', 'style-1' ),
						array( 'uacf7_progressbar_style', '!=', 'style-2' ),
						array( 'uacf7_progressbar_style', '!=', 'style-4' ),
						array( 'uacf7_progressbar_style', '!=', 'style-5' ),

					),
				),

				'uacf7_multistep_progressbar_color_option' => array(
					'id' => 'uacf7_multistep_progressbar_color_option',
					'type' => 'color',
					'label' => __( 'Color Options', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					'multiple' => true,
					'inline' => true,
					'colors' => array(
						'uacf7_multistep_circle_bg_color' => ' Circle Background Color',
						'uacf7_multistep_circle_active_color' => 'Circle Active Color',
						'uacf7_multistep_circle_font_color' => 'Circle Font Color',
						'uacf7_multistep_progress_bg_color' => 'Progressbar Background Color ',
						'uacf7_multistep_progress_line_color' => 'Progressbar Line Color ',
						'uacf7_multistep_step_title_color' => ' Step Title Color',
						'uacf7_multistep_progressbar_title_color' => 'Progressbar Title Color',
					),
				),

				'uacf7_progressbar_size_option' => array(
					'id' => 'uacf7_progressbar_size_option',
					'type' => 'heading',
					'class' => 'heading-inner',
					'label' => __( 'Size Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( ' E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
				),


				'uacf7_multistep_circle_width' => array(
					'id' => 'uacf7_multistep_circle_width',
					'type' => 'number',
					'label' => __( ' Circle Width', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'width', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),

				'uacf7_multistep_circle_height' => array(
					'id' => 'uacf7_multistep_circle_height',
					'type' => 'number',
					'label' => __( ' Circle Height', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'height', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_multistep_font_size' => array(
					'id' => 'uacf7_multistep_font_size',
					'type' => 'number',
					'label' => __( ' Circle Font Size', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'font size', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_multistep_circle_border_radious' => array(
					'id' => 'uacf7_multistep_circle_border_radious',
					'type' => 'number',
					'label' => __( ' Circle Border Radious', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'border radious', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_progressbar_button_style' => array(
					'id' => 'uacf7_progressbar_button_style',
					'type' => 'heading',
					'label' => __( 'Next and Previous Button Style', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'All modifications in this section are applicable to the "Button" of the form.', 'ultimate-addons-cf7' ),
				),
				'uacf7_multistep_button_padding_tb' => array(
					'id' => 'uacf7_multistep_button_padding_tb',
					'type' => 'number',
					'label' => __( ' Padding ( Top - Bottom )', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. 16 (Do not add px or em)', 'ultimate-addons-cf7' ),
					'field_width' => 33,
				),
				'uacf7_multistep_button_padding_lr' => array(
					'id' => 'uacf7_multistep_button_padding_lr',
					'type' => 'number',
					'label' => __( ' Padding ( Left - Right )', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. 16 (Do not add px or em)', 'ultimate-addons-cf7' ),
					'field_width' => 33,
				),

				'uacf7_multistep_button_border_radius' => array(
					'id' => 'uacf7_multistep_button_border_radius',
					'type' => 'number',
					'label' => __( ' Border Radius', 'ultimate-addons-cf7' ),
					'is_pro' => true,
					'field_width' => 33,
				),

				'uacf7_multistep_next_prev_option' => array(
					'id' => 'uacf7_multistep_next_prev_option',
					'type' => 'color',
					'label' => __( 'Color Options', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					'multiple' => true,
					'inline' => true,
					'colors' => array(
						'uacf7_multistep_button_bg' => ' Background Color',
						'uacf7_multistep_button_color' => ' Font Color',
						'uacf7_multistep_button_border_color' => ' Border Color',
						'uacf7_multistep_button_hover_bg' => 'Hover Background Color',
						'uacf7_multistep_button_hover_color' => 'Hover Font Color',
						'uacf7_multistep_button_border_hover_color' => 'Hover Border Color',
					),
					'is_pro' => true,
				),

			),


		), $post_id );

		$value['multistep'] = $multistep;
		return $value;
	}

	function step_start_tag_handler( $tag ) {
		ob_start();
		$form_current = \WPCF7_ContactForm::get_current();
		$meta = uacf7_get_form_option( $form_current->id(), 'multistep' );
		$next_btn = isset( $meta[ 'next_btn_' . $tag->name ] ) ? $meta[ 'next_btn_' . $tag->name ] : esc_html__( 'Next', 'ultimate-addons-cf7' );
		$prev_btn = isset( $meta[ 'prev_btn_' . $tag->name ] ) ? $meta[ 'prev_btn_' . $tag->name ] : esc_html__( 'Previous', 'ultimate-addons-cf7' );

		?>
		<div class="uacf7-step uacf7-step-<?php echo esc_attr( $form_current->id() ); ?> step-content"
			next-btn-text="<?php echo esc_html( $next_btn ); ?>" prev-btn-text="<?php echo esc_html( $prev_btn ); ?>">
			<?php
			return ob_get_clean();
	}
	function step_end_tag_handler( $tag ) {
		ob_start();
		$form_current = \WPCF7_ContactForm::get_current();
		?>
			<p class="uacf7-buttons">
				<button class="uacf7-prev"
					data-form-id="<?php echo esc_attr( $form_current->id() ); ?>"><?php echo esc_html__( 'Previous', 'ultimate-addons-cf7' ); ?></button>
				<button class="uacf7-next"
					data-form-id="<?php echo esc_attr( $form_current->id() ); ?>"><?php echo esc_html__( 'Next', 'ultimate-addons-cf7' ); ?></button>
				<span class="wpcf7-spinner uacf7-ajax-loader"></span>
			</p>
		</div>
		<?php
		return ob_get_clean();

	}
	function uacf7_multistep_progressbar( $tag ) {
		ob_start();
		$form_current = \WPCF7_ContactForm::get_current();
		$steps = $form_current->scan_form_tags( array( 'type' => 'uacf7_step_start' ) );
		$all_steps = apply_filters( 'uacf7_multistep_steps_title', array(), $steps );
		$meta = uacf7_get_form_option( $form_current->id(), 'multistep' );
		?>
		<div class="uacf7-steps steps-form">
			<div class="steps-row setup-panel">
				<?php
				$step_id = 1;
				$step_count = 0;
				$step_name = apply_filters( 'uacf7_multistep_steps_names', array(), $steps );
				$uacf7_multistep_use_step_labels = ! empty( $meta['uacf7_multistep_use_step_labels'] ) ? $meta['uacf7_multistep_use_step_labels'] : '';
				foreach ( $all_steps as $step ) {
					$content = $step;
					?>
					<div class="steps-step">
						<a href="#step-<?php echo esc_attr( $step_id ); ?>" type="button"
							class="btn <?php if ( $step_id == 1 ) {
								echo esc_attr( 'uacf7-btn-active' );
							} else {
								echo esc_attr( 'uacf7-btn-default' );
							} ?> btn-circle"><?php
							  if ( is_array( $step_name ) ) {
								  do_action( 'uacf7_progressbar_image', $step_name[ $step_count ], $form_current->id() );
							  }
							  echo apply_filters( 'uacf7_progressbar_step', esc_attr( $step_id ), $uacf7_multistep_use_step_labels, $content ); ?>
							  </a>
						<p>
							<?php if ( $uacf7_multistep_use_step_labels != 'on' ) {
								echo $content;
							} ?>
						</p>
					</div>
					<?php
					$step_id++;
					$step_count++;
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/*
	 * Generate tag
	 */
	public function tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_step_start',
			__( 'Multistep Start', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_step_start' ],
			array( 'version' => '2' )
		);

		$tag_generator->add(
			'uacf7_step_end',
			__( 'Multistep End', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_step_end' ],
			array( 'version' => '2' )
		);


	}
	static function tg_pane_step_start( $contact_form, $options ) {

		$field_types = array(
			'uacf7_step_start' => array(
				'display_name' => __( 'Multistep Start', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate tag: Step', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		?>

		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_step_start']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_step_start']['description'],
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
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-multi-step-forms/" target="_blank">Multi-step Form</a>'
				); ?>
			</div>
		</header>

		<div class="control-box uacf7-control-box">

		<?php

			$tgg->print( 'field_type', array(
				'select_options' => array(
					'uacf7_step_start' => $field_types['uacf7_step_start']['display_name'],
				),
			) );
		?>

		<?php 

		$tgg->print( 'default_value', array(
			'title' => __( 'Label', 'ultimate-addons-cf7' ),
		) );

		$tgg->print( 'field_name' );
		
		?>


		<fieldset>
			<div class="uacf7-doc-notice uacf7-guide">
				<?php echo esc_html( __( 'To activate the form, enable it from the "Multi-step Form" tab located under the Ultimate Addons for CF7 Options. This tab also contains additional settings.', 'ultimate-addons-cf7' ) ); ?>
			</div>
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


	static function tg_pane_step_end( $contact_form, $options ) {
		$field_types = array(
			'uacf7_step_end' => array(
				'display_name' => __( 'Multistep End', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate tag: Multistep End', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		?>

		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_step_end']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_step_end']['description'],
				array(
					'a' => array( 'href' => true ),
					'strong' => array(),
				),
				array( 'http', 'https' )
			);

			echo $description;
			?></p>
		</header>

		<div class="control-box uacf7-control-box">
			<?php

				$tgg->print( 'field_type', array(
					'select_options' => array(
						'uacf7_step_end' => $field_types['uacf7_step_end']['display_name'],
					),
				) );

				$tgg->print( 'field_name' );
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




	public function uacf7_post_meta_options_multistep_pro( $value, $post_id ) {
		$fields = $value['fields'];

		if ( $post_id != 0 ) {
			// Current Contact Form tags
			$form_current = WPCF7_ContactForm::get_instance( $post_id );

			// $all_steps = $form_current->scan_form_tags( array('type'=>'uacf7_step_start') );
			if ( isset( $form_current ) && is_object( $form_current ) ) {
				if ( method_exists( $form_current, 'scan_form_tags' ) ) {
					// Call the scan_form_tags() method
					$all_steps = $form_current->scan_form_tags( array( 'type' => 'uacf7_step_start' ) );
				} else {
					// Handle case where scan_form_tags() method is not available
					error_log( 'Error: scan_form_tags() method not found in ' . get_class( $form_current ) );
					echo "Error: scan_form_tags() method not found.";
				}
			} else {
				// Handle case where $form_current is not defined or not an object
				error_log( 'Error: $form_current is not defined or is not an object.' );
				echo "Error: Form object not found.";
			}

			$step_titles = array();


			foreach ( $all_steps as $step ) {
				$step_titles[] = ( is_array( $step->values ) && ! empty( $step->values ) ) ? $step->values[0] : '';
			}
			if ( ! empty( array_filter( $all_steps ) ) ) {
				$step_count = 1;
				foreach ( $all_steps as $step ) {

					$fields[ 'uacf7_multistep_step_' . $step_count . '' ] = array(
						'id' => 'uacf7_multistep_step_' . $step_count . '',
						'type' => 'heading',
						'label' => __( 'Step ' . $step_count . '', 'ultimate-addons-cf7' ),
						'is_pro' => true,
					);

					$fields[ 'uacf7_progressbar_image_' . $step->name . '' ] = array(
						'id' => 'uacf7_progressbar_image_' . $step->name . '',
						'type' => 'image',
						'label' => __( 'Add progressbar image for this step', 'ultimate-addons-cf7' ),
						'class' => 'tf-field-class',
						'multiple' => false,
						'inline' => true,
						'is_pro' => true,
						'dependency' => array(
							array( 'uacf7_progressbar_style', '!=', 'style-7' ),
							array( 'uacf7_progressbar_style', '!=', 'style-8' ),
							array( 'uacf7_progressbar_style', '!=', 'style-9' ),
							array( 'uacf7_progressbar_style', '!=', 'style-10' ),
						)
					);
					$fields[ 'uacf7_progressbar_icon_' . $step->name . '' ] = array(
						'id' => 'uacf7_progressbar_icon_' . $step->name . '',
						'type' => 'icon',
						'label' => __( 'Add progressbar icon for this step', 'ultimate-addons-cf7' ),
						'class' => 'tf-field-class',
						'multiple' => false,
						'inline' => true,
						'is_pro' => true,
						'default' => 'fas fa-user',
						'dependency' => array(
							array( 'uacf7_progressbar_style', '!=', 'style-1' ),
							array( 'uacf7_progressbar_style', '!=', 'style-2' ),
							array( 'uacf7_progressbar_style', '!=', 'style-3' ),
							array( 'uacf7_progressbar_style', '!=', 'style-4' ),
							array( 'uacf7_progressbar_style', '!=', 'style-5' ),
							array( 'uacf7_progressbar_style', '!=', 'style-6' ),
						),
					);

					if ( $step_count == 1 ) {
						$fields[ 'next_btn_' . $step->name . '' ] = array(
							'id' => 'next_btn_' . $step->name . '',
							'type' => 'text',
							'label' => __( 'Change next button text for this Step', 'ultimate-addons-cf7' ),
							'field_width' => 50,
							'is_pro' => true,
						);
					} else {
						if ( count( $all_steps ) == $step_count ) {
							$fields[ 'prev_btn_' . $step->name . '' ] = array(
								'id' => 'prev_btn_' . $step->name . '',
								'type' => 'text',
								'label' => __( 'Change previous button text for this Step', 'ultimate-addons-cf7' ),
								'field_width' => 50,
								'is_pro' => true,
							);
						} else {
							$fields[ 'next_btn_' . $step->name . '' ] = array(
								'id' => 'next_btn_' . $step->name . '',
								'type' => 'text',
								'label' => __( 'Change next button text for this Step', 'ultimate-addons-cf7' ),
								'field_width' => 50,
								'is_pro' => true,
							);
							$fields[ 'prev_btn_' . $step->name . '' ] = array(
								'id' => 'prev_btn_' . $step->name . '',
								'type' => 'text',
								'label' => __( 'Change previous button text for this Step', 'ultimate-addons-cf7' ),
								'field_width' => 50,
								'is_pro' => true,
							);
						}
					}
					$fields[ 'desc_title_' . $step->name . '' ] = array(
						'id' => 'desc_title_' . $step->name . '',
						'type' => 'text',
						'label' => __( 'Description title', 'ultimate-addons-cf7' ),
						'placeholder' => __( 'Description title', 'ultimate-addons-cf7' ),
						'is_pro' => true,
						'field_width' => 50,
						'dependency' => array(
							array( 'uacf7_progressbar_style', '==', 'style-6' ),

						),
					);
					$fields[ 'step_desc_' . $step->name . '' ] = array(
						'id' => 'step_desc_' . $step->name . '',
						'type' => 'textarea',
						'label' => __( 'Step description', 'ultimate-addons-cf7' ),
						'placeholder' => __( 'Step description', 'ultimate-addons-cf7' ),
						'is_pro' => true,
						'dependency' => array(
							array( 'uacf7_progressbar_style', '!=', 'style-1' ),
							array( 'uacf7_progressbar_style', '!=', 'style-2' ),
							array( 'uacf7_progressbar_style', '!=', 'style-3' ),
							array( 'uacf7_progressbar_style', '!=', 'style-4' ),
							array( 'uacf7_progressbar_style', '!=', 'style-5' ),
							array( 'uacf7_progressbar_style', '!=', 'style-8' ),
							array( 'uacf7_progressbar_style', '!=', 'style-9' ),
						),
					);

					$fields[ 'step_form_desc_' . $step->name . '' ] = array(
						'id' => 'step_form_desc_' . $step->name . '',
						'type' => 'textarea',
						'label' => __( 'Step Form description', 'ultimate-addons-cf7' ),
						'placeholder' => __( 'Step Form description', 'ultimate-addons-cf7' ),
						'is_pro' => true,
						'dependency' => array(
							array( 'uacf7_progressbar_style', '!=', 'style-1' ),
							array( 'uacf7_progressbar_style', '!=', 'style-2' ),
							array( 'uacf7_progressbar_style', '!=', 'style-3' ),
							array( 'uacf7_progressbar_style', '!=', 'style-4' ),
							array( 'uacf7_progressbar_style', '!=', 'style-5' ),
							array( 'uacf7_progressbar_style', '!=', 'style-6' ),
							array( 'uacf7_progressbar_style', '!=', 'style-8' ),
							array( 'uacf7_progressbar_style', '!=', 'style-9' ),
						),
					);

					$step_count++;
				}
			}
			// uacf7_print_r($all_steps);
			// wp_die();
		}
		// exit;
		$value['fields'] = $fields;
		return $value;
	}


	/*
	 * Change form properties for multistep
	 */
	public function uacf7_properties( $cfform ) {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$form = $cfform->prop( 'form' );

			$mail = $cfform->prop( 'mail' );
			$mail_2 = $cfform->prop( 'mail_2' );
			$multistep_meta = uacf7_get_form_option( $cfform->id(), 'multistep' );

			$uacf7_multistep_is_multistep = isset( $multistep_meta['uacf7_multistep_is_multistep'] ) ? $multistep_meta['uacf7_multistep_is_multistep'] : '';
			$uacf7_enable_multistep_progressbar = isset( $multistep_meta['uacf7_enable_multistep_progressbar'] ) ? $multistep_meta['uacf7_enable_multistep_progressbar'] : '';

			$all_steps = $cfform->scan_form_tags( array( 'type' => 'uacf7_step_start' ) );
			// beaf_print_r($multistep_meta, $all_steps );
			if ( $uacf7_multistep_is_multistep == true ) {
				ob_start();
				// Current Contact Form tags
				$uacf7_multistep_use_step_labels = ! empty( $multistep_meta['uacf7_multistep_use_step_labels'] ) ? $multistep_meta['uacf7_multistep_use_step_labels'] : '';
				$uacf7_multistep_button_padding_tb = $multistep_meta['uacf7_multistep_button_padding_tb'];
				$uacf7_multistep_button_padding_lr = $multistep_meta['uacf7_multistep_button_padding_lr'];
				if ( $uacf7_multistep_button_padding_tb != '' || $uacf7_multistep_button_padding_tb != 0 ) {
					$padding_top = 'padding-top:' . $uacf7_multistep_button_padding_tb . 'px !important;';
					$padding_bottom = 'padding-bottom:' . $uacf7_multistep_button_padding_tb . 'px !important;';
				} else {
					$padding_top = '';
					$padding_bottom = '';
				}
				if ( $uacf7_multistep_button_padding_lr != '' || $uacf7_multistep_button_padding_lr != 0 ) {
					$padding_left = 'padding-left:' . $uacf7_multistep_button_padding_lr . 'px !important;';
					$padding_right = ' padding-right:' . $uacf7_multistep_button_padding_lr . 'px !important;';
				} else {
					$padding_left = '';
					$padding_right = '';
				}
				$next_prev_style = '<style>.uacf7-prev, .uacf7-next, .wpcf7-submit{' . $padding_top . ' ' . $padding_bottom . ' ' . $padding_left . ' ' . $padding_right . '}  </style>';
				echo $next_prev_style;


				if ( ! empty( $all_steps ) ) :
					?>
					<div class="uacf7-steps steps-form" style="display:none">
						<div class="steps-row setup-panel">
							<?php
							$step_id = 1;
							$step_count = 0;
							$step_name = apply_filters( 'uacf7_multistep_steps_names', array(), $all_steps );

							foreach ( $all_steps as $step ) {
								$content = $step;
								?>
								<div class="steps-step">
									<a title-id=".step-<?php echo esc_attr( $step_id ); ?>"
										data-form-id="<?php echo esc_attr( $cfform->id() ); ?>"
										href="#<?php echo esc_attr( $cfform->id() ); ?>step-<?php echo esc_attr( $step_id ); ?>"
										type="button">
									</a>
								</div>
								<?php
								$step_id++;
								$step_count++;
							}
							?>
						</div>
					</div>
				<?php endif; ?>

				<?php
				if ( $uacf7_enable_multistep_progressbar == true ) {
					$uacf7_progressbar_style = $multistep_meta['uacf7_progressbar_style'];
					do_action( 'uacf7_multistep_before_form', $cfform->id() );
					?>
					<?php
					$uacf7_multistep_progressbar_title_color = isset( $multistep_meta['uacf7_multistep_progressbar_title_color'] ) ? $multistep_meta['uacf7_multistep_progressbar_title_color'] : '';
					if ( $uacf7_progressbar_style == 'default' && ! empty( $uacf7_multistep_progressbar_title_color ) ) :
						?>
						<style>
							.steps-form .steps-row .steps-step p {
								<?php if ( ! empty( $uacf7_multistep_progressbar_title_color ) ) {
									echo 'color: ' . esc_attr( $uacf7_multistep_progressbar_title_color ) . ';';
								} ?>
							}

							.uacf7-steps .uacf7-next,
							.uacf7-steps .uacf7-next {
								<?php if ( ! empty( $uacf7_multistep_button_padding_tb ) ) {
									echo 'padding: ' . esc_attr( $uacf7_multistep_button_padding_tb ) . esc_attr( $uacf7_multistep_button_padding_lr ) . ';';
								} ?>
							}
						</style>
					<?php endif; ?>
					<?php if ( ! empty( $all_steps ) ) : ?>

						<?php
							if ( $uacf7_progressbar_style == 'style-1' ) {
								$progressbar_class = 'progressbar-style-1';
							}elseif ( $uacf7_progressbar_style == 'style-8' ) {
								$progressbar_class = 'progressbar-style-8';
							}elseif ( $uacf7_progressbar_style == 'style-9' ) {
								$progressbar_class = 'progressbar-style-9';
							}else{
								$progressbar_class = '';
							}
						?>
						
						<?php if ($uacf7_progressbar_style == 'style-8' || $uacf7_progressbar_style == 'style-9'){ ?>
						<div class="uacf7-steps-wrapper">
							<span class="scroll-btn left-btn"><i class="fa-solid fa-angle-left"></i></span>
						<?php } ?>
						<div class="uacf7-steps steps-form <?php echo esc_attr( $progressbar_class ); ?>">
							<div class="steps-row setup-panel">
								<?php
								$step_id = 1;
								$step_count = 0;
								$step_name = apply_filters( 'uacf7_multistep_steps_names', '', $all_steps );

								foreach ( $all_steps as $step ) {
									// $content = $step->values[0];
									$content = isset( $step->values[0] ) ? $step->values[0] : '';
									?>

									<div class="steps-step">
										<a title-id=".step-<?php echo esc_attr( $step_id ); ?>"
											data-form-id="<?php echo esc_attr( $cfform->id() ); ?>"
											href="#<?php echo esc_attr( $cfform->id() ); ?>step-<?php echo esc_attr( $step_id ); ?>" type="button"
											class="btn <?php if ( $step_id == 1 ) {
												echo esc_attr( 'uacf7-btn-active' );
											} else {
												echo esc_attr( 'uacf7-btn-default' );
											} ?> btn-circle"><?php
											  if ( is_array( $step_name ) ) {
												  do_action( 'uacf7_progressbar_image', $step_name[ $step_count ], $cfform->id() );
											  }
											  if ( $uacf7_progressbar_style == 'style-1' || $uacf7_progressbar_style == 'style-8' || $uacf7_progressbar_style == 'style-9' ) {
												  if ( $uacf7_multistep_use_step_labels != true ) {
													  echo '<p>' . esc_html( $content ) . '</p>';
												  } else {
													  echo esc_attr( $step_id );
												  }
											  } else {
												  echo esc_attr( $step_id );
											  } ?>
										</a>
										<div class="steps-info">
											<?php
												if ( $uacf7_multistep_use_step_labels != true && $uacf7_progressbar_style != 'style-1' && $uacf7_progressbar_style != 'style-4' && $uacf7_progressbar_style != 'style-8' && $uacf7_progressbar_style != 'style-9' ) {
													echo '<p>' . esc_html( $content ) . '</p>';
													if($uacf7_progressbar_style == 'style-7' || $uacf7_progressbar_style == 'style-10' ){
														echo '<span>'.esc_html($multistep_meta['step_desc_'.$step->name]).'</span>';
													}
												}
											?>
										</div>
									</div>
									<?php
									$step_id++;
									$step_count++;
								}
								// uacf7_print_r($step_name);
		
								?>
							</div>
						</div>
						<?php if ($uacf7_progressbar_style == 'style-8' || $uacf7_progressbar_style == 'style-9'){ ?>
							<span class="scroll-btn right-btn"><i class="fa-solid fa-angle-right"></i></span>
						</div>
						<?php } ?>
					<?php endif; ?>
				<?php
				}

				$progressbar = ob_get_clean();
				ob_start();
				echo apply_filters( 'uacf7_progressbar_html', $progressbar, $form, $cfform->id(), $all_steps );
				ob_start();
				?>
				<div class="uacf7-multisetp-form">
					<?php echo $form; ?>
				</div>
				<?php
				$form_html = ob_get_clean();

				echo apply_filters( 'uacf7_form_html', $form_html );
				$multistep_form = ob_get_clean();
				$form_data = $multistep_form;

			} else {
				$form_data = $form;
			}

			$cfform->set_properties( array(
				'form' => $form_data,
				'mail' => $mail,
				'mail_2' => $mail_2,
			) );
		}

		// return $properties;
	}

	public function check_fields_validation() {
		if ( ! wp_verify_nonce( $_REQUEST['ajax_nonce'], 'uacf7-multistep' ) ) {
			exit( esc_html__( "Security error", 'ultimate-addons-cf7' ) );
		}

		$current_step_fields = explode( ',', $_REQUEST['current_fields_to_check'] );

		// Validation with Repeater 
		$validation_fields = explode( ',', $_REQUEST['validation_fields'] );
		$tag_name = [];
		$tag_validation = [];
		$tag_type = [];
		$file_error = [];
		$count = '1';
		for ( $x = 0; $x < count( $validation_fields ); $x++ ) {
			$field = explode( ':', $validation_fields[ $x ] );
			$name = isset( $field[1] ) ? $field[1] : '';
			$name_array = explode( "__", $name );
			$replace = '__' . $count . '';
			$tag_name[] = $name_array[0];
			$tag_validation[ $field[0] . $x ] = $name;
			$tag_type[] = $field[0];
			$count++;
		}
		$form = wpcf7_contact_form( $_REQUEST['form_id'] );
		$all_form_tags = $form->scan_form_tags();
		$invalid_fields = false;
		require_once WPCF7_PLUGIN_DIR . '/includes/validation.php';
		$result = new \WPCF7_Validation();
		$tags = array_filter(
			$all_form_tags, function ($v, $k) use ($tag_name) {
				return in_array( $v->name, $tag_name );
			}, ARRAY_FILTER_USE_BOTH
		);
		$form->validate_schema(
			array(
				'text' => true,
				'file' => false,
				'field' => $tag_name,
			),
			$result
		);
		foreach ( $tags as $tag ) {
			$type = $tag->type;
			if ( 'file' != $type && 'file*' != $type ) {
				$result = apply_filters( "wpcf7_validate_{$type}", $result, $tag );

			} elseif ( 'file*' === $type || 'file' === $type ) {
				$fdir = $_REQUEST[ $tag->name ];
				if ( $fdir ) {
					$_FILES[ $tag->name ] = array(
						'name' => wp_basename( $fdir ),
						'tmp_name' => $fdir,
					);
				}
				$file = $_FILES[ $tag->name ];
				//$file = $_REQUEST[$tag->name];
				$args = array(
					'tag' => $tag,
					'name' => $tag->name,
					'required' => $tag->is_required(),
					'filetypes' => $tag->get_option( 'filetypes' ),
					'limit' => $tag->get_limit_option(),
				);
				$args['schema'] = $form->get_schema();
				$new_files = wpcf7_unship_uploaded_file( $file, $args );
				if ( is_wp_error( $new_files ) ) {
					$result->invalidate( $tag, $new_files );
				}
				$result = apply_filters( "wpcf7_validate_{$type}", $result, $tag, array( 'uploaded_files' => $new_files, ) );

				if ( isset( $_REQUEST[ $tag->name . '_size' ] ) ) {
					$file_size = $_REQUEST[ $tag->name . '_size' ];
					if ( $file_size > $tag->get_limit_option() ) {
						$file_error = array(
							'into' => 'span.wpcf7-form-control-wrap[data-name = ' . esc_attr( $tag->name ) . ']',
							'message' => 'The uploaded file is too large.',
							'idref' => null,
						);
					}
				}
			}

		}
		// $result = apply_filters('wpcf7_validate', $result, $tags); 
		$is_valid = $result->is_valid();
		if ( ! $is_valid ) {
			$invalid_fields = $this->prepare_invalid_form_fields( $result, $tag_validation );
		}
		if ( ! empty( $file_error ) ) {
			$invalid_fields[] = $file_error;
		}
		if ( ! empty( $invalid_fields ) ) {
			$is_valid = false;
		} else {
			$invalid_fields = false;
		}

		echo ( json_encode( array(
			'is_valid' => $is_valid,
			'invalid_fields' => $invalid_fields,
		)
		)
		);

		wp_die();
	}

	private function prepare_invalid_form_fields( $result, $tag_validation ) {
		$invalid_fields = array();

		// Validation with Repeater 
		$count = 1;
		$invalid_data = [];
		foreach ( (array) $result->get_invalid_fields() as $name => $field ) {
			$invalid_data[ $name ] = array(
				'name' => $name,
				'message' => $field['reason'],
				'idref' => $field['idref'],
			);
		}
		foreach ( $tag_validation as $key => $value ) {
			$name = explode( "__", $value );
			$name = $name[0];
			if ( ! empty( $invalid_data[ $name ] ) ) {
				$field = $invalid_data[ $name ];
				$invalid_fields[] = array(
					'into' => 'span.wpcf7-form-control-wrap[data-name = ' . esc_attr( $value ) . ']',
					'message' => $field['message'],
					'idref' => $field['idref'],
				);
			}
		}
		return $invalid_fields;
	}

}
new UACF7_MULTISTEP();
