<?php

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	echo "You are not allowed to access directly";
	exit();
}



class UACF7_SUBMISSION_ID {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'submission_id_public_assets_loading' ] );

		add_action( 'admin_init', [ $this, 'submission_tag_generator' ] );
		add_action( 'wpcf7_init', [ $this, 'submission_id_add_shortcodes' ] );
		add_action( 'wp_ajax_uacf7_update_submission_id', [ $this, 'uacf7_update_submission_id' ] );
		add_action( 'wp_ajax_nopriv_uacf7_update_submission_id', [ $this, 'uacf7_update_submission_id' ] );

		// add_filter( 'wpcf7_mail_sent', [ $this, 'submission_id_update' ] );
		add_filter( 'wpcf7_mail_components', [ $this, 'submission_id_custom_cf7_mail_subject' ], 10, 2 );

		// Submission ID Update into Database free
		add_action( 'uacf7_submission_id_insert', [ $this, 'uacf7_submission_id_insert_callback' ], 10, 4 );
		// UAC7 option Panel
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_submission_id' ), 20, 2 );

		add_action( 'wpcf7_before_send_mail', [ $this, 'uacf7_submission_check' ], 10, 3 );
		// add_filter( 'wpcf7_load_js', '__return_false' );

		require_once 'inc/submission-id.php';
	}


	function uacf7_post_meta_options_submission_id( $value, $post_id ) {

		$submission_id = apply_filters( 'uacf7_post_meta_options_submission_id_pro', $data = array(
			'title' => __( 'Submission ID', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-fingerprint',
			'checked_field' => 'uacf7_submission_id_enable',
			'fields' => array(
				'uacf7_submission_id_heading' => array(
					'id' => 'uacf7_submission_id_heading',
					'type' => 'heading',
					'label' => __( 'Unique Submission ID Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Add an unique id to every form submission to keep a record of each submission. The ID can be added on the "Subject Line" of your form. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/unique-id-for-contact-form-7/" target="_blank" rel="noopener">Example</a>'
					)
				),

				'submission_id_docs' => array(
					'id' => 'submission_id_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/unique-id-for-contact-form-7/" target="_blank" rel="noopener">Submission ID</a>'
					)
				),

				'uacf7_submission_id_enable' => array(
					'id' => 'uacf7_submission_id_enable',
					'type' => 'switch',
					'label' => __( ' Enable Submission ID', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
				),
				'uacf7_submission_form_options_heading' => array(
					'id' => 'uacf7_submission_form_options_heading',
					'type' => 'heading',
					'label' => __( 'Submission Option ', 'ultimate-addons-cf7' ),
				),
				'uacf7_submission_id' => array(
					'id' => 'uacf7_submission_id',
					'type' => 'number',
					'label' => __( ' Submission ID Starts from ', 'ultimate-addons-cf7' ),
					'placeholder' => __( ' 1 ', 'ultimate-addons-cf7' ),
					'description' => __( 'Enter the starting number for the countdown, for example, 101. The default setting is 1.', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_submission_id_step' => array(
					'id' => 'uacf7_submission_id_step',
					'type' => 'number',
					'label' => __( 'ID Step Increment ', 'ultimate-addons-cf7' ),
					'placeholder' => __( ' 1 ', 'ultimate-addons-cf7' ),
					'description' => __( ' Set how much the number will increase with each submission. For instance, if you set it to 2 and the ID starts from 101, the number will increment in the following sequence with each submission: 101, 103, 105, and so on. The default setting is 1.', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_submission_id_send_to_sub_line' => array(
					'id' => 'uacf7_submission_id_send_to_sub_line',
					'type' => 'switch',
					'label' => __( 'Show Submission ID in the Mail Subject Line (Dynamic)', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 50,
				),
				'uacf7_submission_id_place' => array(
					'id' => 'uacf7_submission_id_place',
					'type' => 'select',
					'label' => __( 'ID Placement on the Subject Line', 'ultimate-addons-cf7' ),
					'options' => array(
						'left' => 'Left',
						'right' => 'Right',
						'only_sub_id' => 'Show only Submission ID, skip Subject Text'
					),
					'default' => 'left',
					'field_width' => 50,
					'dependency' => array( 'uacf7_submission_id_send_to_sub_line', '==', true ),
				),
				'submission_docs_help' => array(
					'id' => 'submission_docs_help',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Note: You can use the Submission Shortcode to the mail subject line to see the ID number : <i>[uacf7_submission_id-your_id]</i> <br><b>And if you want to use the manual option then be sure to turn off dynamic otherwise it will be duplicated</b>', 'ultimate-addons-cf7' )
					)
				),
			),


		), $post_id );

		$value['submission_id'] = $submission_id;
		return $value;
	}

	/** Starts Loading Essential JS & CSS */
	public function submission_id_public_assets_loading() {

		wp_enqueue_script( 'submission_id_public_js', UACF7_URL . 'addons/submission-id/assets/public/js/public-submission-id.js', [ 'jquery' ], 'WPCF7_VERSION', true );
		wp_enqueue_style( 'submission_id_public_css', UACF7_URL . 'addons/submission-id/assets/public/css/public-submission-id.css', [], 'UAFC7_VERSION', true, 'all' );
		wp_localize_script( 'submission_id_public_js', 'submission_id_obj', [ 
			"ajaxurl" => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'uacf7-submission-id-nonce' ),

		] );
	}
	/** Ends Loading Essential JS & CSS */

	public function uacf7_submission_check( $form, $abort, $submission ) {
		$form_id = $form->id();
		// Get the submitted data
		$submittedData = $submission->get_posted_data();

		$settingData = uacf7_get_form_option( $form_id, 'submission_id' );
		$uacf7_submission_id_enable = isset( $settingData['uacf7_submission_id_enable'] ) ? $settingData['uacf7_submission_id_enable'] : false;

		if ( $submission && $uacf7_submission_id_enable == true ) {
			$stored_submission_id  = isset( $settingData['uacf7_submission_id'] ) ? $settingData['uacf7_submission_id'] : 0;
			$step_counter = isset( $settingData['uacf7_submission_id_step'] ) ? $settingData['uacf7_submission_id_step'] : 0;

			// Scan form tags to find the specific field
			$tags = $form->scan_form_tags();
			foreach ( $tags as $tag ) {
				if ( $tag->type == 'uacf7_submission_id' ) {
					// Assuming 'name' is the name attribute of your form field
					$field_name = $tag->name;

					// Get the current value of the specific tag
					$submitted_value  = isset( $submittedData[ $field_name ] ) ? $submittedData[ $field_name ] : '';

					if ( $stored_submission_id  > $submitted_value || $submitted_value != $stored_submission_id ) {
						// Update the value
						$new_value = $stored_submission_id ;

						// Override the submitted data
						$submittedData[ $field_name ] = $new_value;

						// Use reflection to update the posted data
						$reflection = new ReflectionClass( $submission );
						$posted_data_property = $reflection->getProperty( 'posted_data' );
						$posted_data_property->setAccessible( true );
						$posted_data_property->setValue( $submission, $submittedData );

						// Update the value for setting value updated
						$new_valueSetting = ( $step_counter > 0 ) ? ( $new_value + $step_counter ) : ( $new_value + 1 );

						$meta = uacf7_get_form_option( $form->id(), '' );
						$meta['submission_id']['uacf7_submission_id'] = $new_valueSetting;
						update_post_meta( $form->id(), 'uacf7_form_opt', $meta );
					} else {
						$valueIncreasing = '';

						if ( $step_counter > 0 ) {
							$valueIncreasing .= $stored_submission_id  + $step_counter;
						} else {
							$valueIncreasing .= $stored_submission_id  + 1;
						}
						$meta = uacf7_get_form_option( $form->id(), '' );
						$meta['submission_id']['uacf7_submission_id'] = $valueIncreasing;
						update_post_meta( $form->id(), 'uacf7_form_opt', $meta );
					}
				}
			}
		}
	}

	/**
	 * Submission ID Realtime update in the Frontend
	 */
	public function uacf7_update_submission_id() {

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'uacf7-submission-id-nonce' ) ) {
			exit( esc_html__( "Security error", 'ultimate-addons-cf7' ) );
		}
		$form_id = $_POST['form_id'];
		$submission = uacf7_get_form_option( $form_id, 'submission_id' );
		$meta_data = isset( $submission['uacf7_submission_id'] ) ? $submission['uacf7_submission_id'] : 0;

		echo wp_send_json( [ 
			'form_id' => $form_id,
			'meta_data' => $meta_data
		] );

	}

	/**
	 * Submission ID Update into Database
	 */
	public function uacf7_submission_id_insert_callback( $uacf7_db_id, $form_id, $insert_data, $tags ) {

		$submission = uacf7_get_form_option( $form_id, 'submission_id' );

		$uacf7_submission_id_enable = isset( $submission['uacf7_submission_id_enable'] ) ? $submission['uacf7_submission_id_enable'] : false;

		if ( $uacf7_submission_id_enable == true ) {

			$submission_value = isset( $submission['uacf7_submission_id'] ) ? $submission['uacf7_submission_id'] : 0;
			if ( $submission_value != '' || $submission_value != null || $submission_value != 0 ) {

				global $wpdb;
				$table_name = $wpdb->prefix . 'uacf7_form';
				$id = $uacf7_db_id;

				// update submission id existing database
				$sql = $wpdb->prepare( "UPDATE $table_name SET submission_id= %s WHERE id= %s", $submission_value, $id );


				$wpdb->query( $sql );
			}
		}

	}

	public function submission_id_update( $form ) {

		$submission = uacf7_get_form_option( $form->id(), 'submission_id' );
		$uacf7_submission_id_enable = isset( $submission['uacf7_submission_id_enable'] ) ? $submission['uacf7_submission_id_enable'] : false;

		if ( $uacf7_submission_id_enable == true ) {

			$getCurrentData = isset( $submission['uacf7_submission_id'] ) ? $submission['uacf7_submission_id'] : 0;
			$step_counter = isset( $submission['uacf7_submission_id_step'] ) ? $submission['uacf7_submission_id_step'] : 0;




		}

	}

	/**
	 * Submission TAG Generator
	 */
	public function submission_id_add_shortcodes() {

		wpcf7_add_form_tag(
			array( 'uacf7_submission_id', 'uacf7_submission_id*' ),
			array( $this, 'uacf7_submission_id_tag_handler_callback' ),
			array( 'name-attr' => true )
		);
	}

	public function uacf7_submission_id_tag_handler_callback( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		/** Enable / Disable Submission ID */
		$wpcf7 = WPCF7_ContactForm::get_current();
		$formid = $wpcf7->id();
		$submission = uacf7_get_form_option( $formid, 'submission_id' );
		$uacf7_submission_id_enable = isset( $submission['uacf7_submission_id_enable'] ) ? $submission['uacf7_submission_id_enable'] : false;

		if ( $uacf7_submission_id_enable != true ) {
			return;
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );


		if ( $validation_error ) {
			$class .= 'wpcf7-not-valid';
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
		$value = $tag->values;
		$default_value = $tag->get_default_option( $value );


		$submission_id = isset( $submission['uacf7_submission_id'] ) ? intval($submission['uacf7_submission_id']) : '';

		$atts['value'] = esc_attr($submission_id);

		$atts['name'] = $tag->name;

		$atts = wpcf7_format_atts( $atts );

		ob_start();

		?>
		<span class="wpcf7-form-control-wrap <?php echo sanitize_html_class( $tag->name ); ?>"
			data-name="<?php echo sanitize_html_class( $tag->name ); ?>">

			<input hidden id="uacf7_<?php echo esc_attr( $tag->name ); ?>" <?php echo $atts; ?>>
			<span><?php echo $validation_error; ?></span>
		</span>

		<?php

		$submission_buffer = ob_get_clean();

		return $submission_buffer;
	}

	/*
	 * Generate tag - Submission ID
	 */
	public function submission_tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_submission_id',
			__( 'Submission ID', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_submission_id' ],
			array( 'version' => '2' )
		);

	}

	public static function tg_pane_submission_id( $contact_form, $options ) {

		$field_types = array(
			'uacf7_submission_id' => array(
				'display_name' => __( 'Submission ID', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate a Unique Submission ID.', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		?>

		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_submission_id']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_submission_id']['description'],
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
					'<a href="https://themefic.com/docs/uacf7/free-addons/unique-id-for-contact-form-7/" target="_blank">Unique Submission ID</a>'
				); ?>
			</div>
		</header>

		<div class="control-box uacf7-control-box">

			<?php

			$tgg->print( 'field_type', array(
				'select_options' => array(
					'uacf7_submission_id' => $field_types['uacf7_submission_id']['display_name'],
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

	public function submission_id_custom_cf7_mail_subject( $components, $form ) {

		$form_id = $form->id();
		$submission = uacf7_get_form_option( $form_id, 'submission_id' );

		$meta_data = isset( $submission['uacf7_submission_id'] ) ? $submission['uacf7_submission_id'] : 0;

		$uacf7_submission_id_enable = isset( $submission['uacf7_submission_id_enable'] ) ? $submission['uacf7_submission_id_enable'] : false;

		$uacf7_submission_id_send_to_sub_line = isset( $submission['uacf7_submission_id_send_to_sub_line'] ) ? $submission['uacf7_submission_id_send_to_sub_line'] : false;

		$uacf7_submission_id_place = isset( $submission['uacf7_submission_id_place'] ) ? $submission['uacf7_submission_id_place'] : false;

		if ( $uacf7_submission_id_enable == true && $uacf7_submission_id_send_to_sub_line == true ) {
			if ( $uacf7_submission_id_place === 'left' ) {
				$components['subject'] = $meta_data . ' ' . $components['subject'];
			} elseif ( $uacf7_submission_id_place === 'right' ) {
				$components['subject'] = $components['subject'] . ' ' . $meta_data;
			} elseif ( $uacf7_submission_id_place === 'only_sub_id' ) {
				$components['subject'] = $meta_data;
			}
		}
		return $components;
	}

}
new UACF7_SUBMISSION_ID();










