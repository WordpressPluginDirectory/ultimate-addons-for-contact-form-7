<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Pre Populate Classs
 */
class UACF7_PDF_GENERATOR {

	private static $pdf_url = '';

	/*
	 * Construct function
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_admin_script' ) );
		add_filter( 'wpcf7_mail_components', array( $this, 'uacf7_wpcf7_mail_components' ), 10, 3 );
		// add_filter( 'wpcf7_load_js', '__return_false' );
		add_action( 'wp_ajax_uacf7_get_generated_pdf', array( $this, 'uacf7_get_generated_pdf' ) );

		add_action('wpcf7_mail_sent', [$this, 'store_pdf_url']);
		
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_pdf_generator' ), 18, 2 );
		add_filter( 'uacf7_post_meta_import_export', array( $this, 'uacf7_post_meta_import_export_pdf_generator' ), 18, 2 );

		require_once( 'inc/functions.php' );

	}

	public function store_pdf_url($contact_form) {
		$form_id = $contact_form->id();
		$pdf_url = get_transient('uacf7_pdf_url_' . $form_id);
	
		if ($pdf_url) {
	
			add_filter('wpcf7_ajax_json_echo', function($response) use ($pdf_url) {
				if (isset($response['status']) && $response['status'] === 'mail_sent') {
					$response['pdf_url'] = $pdf_url;
				}
				return $response;
			}, 10, 2);
	
			delete_transient('uacf7_pdf_url_' . $form_id);
		}
	}

	/*
	 * Enqueue script Backend
	 */

	public function wp_enqueue_admin_script() {

		wp_enqueue_script( 'pdf-generator-admin', UACF7_ADDONS . '/pdf-generator/assets/js/pdf-generator-admin.js', array( 'jquery' ), true );
		$pdf_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		$pdf_settings['ajaxurl'] = admin_url( 'admin-ajax.php' );
		$pdf_settings['nonce'] = wp_create_nonce( 'uacf7-pdf-generator' );
		wp_localize_script( 'jquery', 'pdf_settings', $pdf_settings );

		// require UACF7_PATH . 'third-party/vendor/autoload.php';

	}

	public function uacf7_post_meta_options_pdf_generator( $value, $post_id ) {


		$pdf_generator = apply_filters( 'uacf7_post_meta_options_pdf_generator_pro', $data = array(
			'title' => __( 'PDF Generator', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-file-pdf',
			'checked_field' => 'uacf7_enable_pdf_generator',
			'fields' => array(
				'uacf7_pdf_label' => array(
					'id' => 'uacf7_pdf_label',
					'type' => 'heading',
					'label' => __( 'PDF Generator Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Generate a PDF from submissions and send it to admin and the submitter\'s email. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/contact-form-7-pdf-generator/" target="_blank" rel="noopener">Example</a>'
					)
				),
				'pdf_generator_docs' => array(
					'id' => 'pdf_generator_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-pdf-generator/" target="_blank" rel="noopener">PDF Generator</a>'
					)
				),
				'uacf7_enable_pdf_generator' => array(
					'id' => 'uacf7_enable_pdf_generator',
					'type' => 'switch',
					'label' => __( ' Enable PDF Generator ', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 100,
				),
				'pdf_generator_form_options_heading' => array(
					'id' => 'pdf_generator_form_options_heading',
					'type' => 'heading',
					'label' => __( 'PDF Option ', 'ultimate-addons-cf7' ),
				),
				'uacf7_pdf_disable_header_footer' => array(
					'id' => 'uacf7_pdf_disable_header_footer',
					'type' => 'checkbox',
					'label' => __( 'Disable Header and Footer of PDF', 'ultimate-addons-cf7' ),
					'options' => array(
						'header' => 'Disable Header',
						'footer' => 'Disable Footer'
					),
					'field_width' => 100,
					'inline' => true
				),

				'uacf7_pdf_name' => array(
					'id' => 'uacf7_pdf_name',
					'type' => 'text',
					'label' => __( 'PDF Name ', 'ultimate-addons-cf7' ),
					'subtitle' => __( "For instance, if you enter 'website-submission' as the file name, the resulting PDF will be named 'website-submission.pdf'.", 'ultimate-addons-cf7' ),
					'placeholder' => __( 'E.g. website-submission', 'ultimate-addons-cf7' ),
					'field_width' => 50,

				),
				'pdf_send_to' => array(
					'id' => 'pdf_send_to',
					'type' => 'select',
					'label' => __( 'PDF Send To ', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Choose whether you want both Mail 1 and Mail 2 users to receive the PDF as an attachment, or just one of them.', 'ultimate-addons-cf7' ),
					'options' => array(
						'both' => 'Both',
						'mail-1' => 'Mail 1',
						'mail-2' => 'Mail 2',
					),
					'field_width' => '50'
				),
				'uacf7_customize_pdf_header' => array(
					'id' => 'uacf7_customize_pdf_header',
					'type' => 'heading',
					'label' => __( 'PDF Header Settings', 'ultimate-addons-cf7' ),

				),
				// 'uacf7_pdf_generator_mpdf_tags' => array(
				//     'id'        => 'uacf7_pdf_generator_mpdf_tags',
				//     'type'      => 'notice',
				//     'label'     => __( 'm-PDF Tags ', 'ultimate-addons-cf7' ),
				//     'class' => 'tf-field-class',
				//     'content' => '
				//     {PAGENO}, {DATE j-m-Y}, {nb}, {nbpg}
				//     ',
				// ),
				'pdf_header_upload_image' => array(
					'id' => 'pdf_header_upload_image',
					'type' => 'image',
					'label' => __( 'Header Image ', 'ultimate-addons-cf7' ),

				),

				'pdf_header_color' => array(
					'id' => 'pdf_header_color',
					'type' => 'color',
					'label' => __( 'Header Content Color ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
				),
				'pdf_header_bg_color' => array(
					'id' => 'pdf_header_bg_color',
					'type' => 'color',
					'label' => __( 'Header Background Color ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
				),
				'customize_pdf_header' => array(
					'id' => 'customize_pdf_header',
					'label' => __( 'Header Content', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Some tags you can use - Page numbers & date Tags : {PAGENO}, {DATE j-m-Y}, {nb}, {nbpg}. <br> Use any php <a href="https://www.php.net/manual/en/function.date.php" target="_blank">Date</a> format for date shortcode {DATE format} ', 'ultimate-addons-cf7' ),
					'type' => 'editor',

				),
				'uacf7_customize_pdf_body' => array(
					'id' => 'uacf7_customize_pdf_body',
					'type' => 'heading',
					'label' => __( 'PDF Body Settings', 'ultimate-addons-cf7' ),
				),

				'pdf_bg_upload_image' => array(
					'id' => 'pdf_bg_upload_image',
					'type' => 'image',
					'label' => __( 'Body Background Image ', 'ultimate-addons-cf7' ),

				),
				'pdf_content_color' => array(
					'id' => 'pdf_content_color',
					'type' => 'color',
					'label' => __( 'Body Content Color ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
				),
				'pdf_content_bg_color' => array(
					'id' => 'pdf_content_bg_color',
					'type' => 'color',
					'label' => __( 'Body Background Color ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
				),



				'customize_pdf' => array(
					'id' => 'customize_pdf',
					'label' => __( 'Body Content ', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'If you wish to include extra content in the body of the PDF.', 'ultimate-addons-cf7' ),
					'type' => 'editor',

				),
				'uacf7_pdf_form_tags' => array(
					'id' => 'uacf7_pdf_form_tags',
					'type' => 'callback',
					'function' => 'uacf7_pdf_form_tags_callback',
					'argument' => $post_id,

				),

				'uacf7_customize_pdf_footer' => array(
					'id' => 'uacf7_customize_pdf_footer',
					'type' => 'heading',
					'label' => __( 'PDF Footer Settings', 'ultimate-addons-cf7' ),
				),

				// 'uacf7_pdf_footer_background_image' => array(
				//     'id'        => 'uacf7_pdf_footer_background_image',
				//     'type'      => 'image',
				//     'label'     => __( 'PDF Footer Background Image ', 'ultimate-addons-cf7' ),

				// ),


				'pdf_footer_color' => array(
					'id' => 'pdf_footer_color',
					'type' => 'color',
					'label' => __( 'Footer Content Color ', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
					'field_width' => 50,
				),

				'pdf_footer_bg_color' => array(
					'id' => 'pdf_footer_bg_color',
					'type' => 'color',
					'label' => __( 'Footer Background Color ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
				),

				'customize_pdf_footer' => array(
					'id' => 'customize_pdf_footer',
					'label' => __( 'Footer Content', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Some tags you can use - Page numbers & date Tags : {PAGENO}, {DATE j-m-Y}, {nb}, {nbpg}. <br> Use any php <a href="https://www.php.net/manual/en/function.date.php" target="_blank">Date</a> format for date shortcode {DATE format} ', 'ultimate-addons-cf7' ),
					'type' => 'editor',

				),
				'uacf7_pdf_form_download' => array(
					'id' => 'uacf7_pdf_form_download',
					'type' => 'heading',
					'label' => __( 'Download Form After Submit', 'ultimate-addons-cf7' ),
				),
				'uacf7_enable_pdf_form_download' => array(
					'id' => 'uacf7_enable_pdf_form_download',
					'type' => 'switch',
					'label' => __( ' Enable PDF Download on Submission ', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 100,
					'is_pro' => true
				),
				'uacf7_pdf_custom_css' => array(
					'id' => 'uacf7_pdf_custom_css',
					'type' => 'heading',
					'label' => __( 'Custom CSS for PDF', 'ultimate-addons-cf7' ),
				),
				'custom_pdf_css' => array(
					'id' => 'custom_pdf_css',
					'type' => 'code_editor',

				),
			),


		), $post_id );

		$value['pdf_generator'] = $pdf_generator;
		return $value;
	}



	// Generate PDF and export form ultimate db
	public function uacf7_get_generated_pdf() {
		if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'uacf7-pdf-generator' ) ) {
			exit( esc_html__( "Security error", 'ultimate-addons-cf7' ) );
		}

		$form_id = ! empty( $_POST['form_id'] ) ? $_POST['form_id'] : '';
		$data_id = ! empty( $_POST['id'] ) ? $_POST['id'] : '';
		require UACF7_PATH . 'third-party/vendor/autoload.php';

		// Pdf get Meta Option
		$pdf = uacf7_get_form_option( $form_id, 'pdf_generator' );

		$enable_pdf = isset( $pdf['uacf7_enable_pdf_generator'] ) ? $pdf['uacf7_enable_pdf_generator'] : 0;
		if ( $enable_pdf != true ) {
			die;
		}

		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];
		$url = $upload_dir['baseurl'];
		global $wpdb;
		$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "uacf7_form WHERE id = %s AND form_id = %s", $data_id, $form_id ) );

		$uacf7_pdf_name          = ! empty( $pdf['uacf7_pdf_name'] ) ? $pdf['uacf7_pdf_name'] : get_the_title( $form_id );
		$disable_header          = ! empty( $pdf['uacf7_pdf_disable_header_footer'] ) && in_array( 'header', $pdf['uacf7_pdf_disable_header_footer'] ) ? true : false;
		$disable_footer          = ! empty( $pdf['uacf7_pdf_disable_header_footer'] ) && in_array( 'footer', $pdf['uacf7_pdf_disable_header_footer'] ) ? true : false;
		$customize_pdf           = ! empty( $pdf['customize_pdf'] ) ? $pdf['customize_pdf'] : '';
		$pdf_bg_upload_image     = ! empty( $pdf['pdf_bg_upload_image'] ) ? $pdf['pdf_bg_upload_image'] : '';
		$customize_pdf_header    = ! empty( $pdf['customize_pdf_header'] ) ? $pdf['customize_pdf_header'] : '';
		$pdf_header_upload_image = ! empty( $pdf['pdf_header_upload_image'] ) ? $pdf['pdf_header_upload_image'] : '';
		$pdf_header_img_height   = ! empty( $pdf['pdf_header_img_height'] ) ? $pdf['pdf_header_img_height'] : '';
		$pdf_header_img_width    = ! empty( $pdf['pdf_header_img_width'] ) ? $pdf['pdf_header_img_width'] : '';
		$pdf_header_img_aline    = ! empty( $pdf['pdf_header_img_aline'] ) ? $pdf['pdf_header_img_aline'] : '';
		$customize_pdf_footer    = ! empty( $pdf['customize_pdf_footer'] ) ? $pdf['customize_pdf_footer'] : '';
		$custom_pdf_css          = ! empty( $pdf['custom_pdf_css'] ) ? $pdf['custom_pdf_css'] : '';
		$pdf_content_color       = ! empty( $pdf['pdf_content_color'] ) ? $pdf['pdf_content_color'] : '000';
		$pdf_content_bg_color    = ! empty( $pdf['pdf_content_bg_color'] ) ? $pdf['pdf_content_bg_color'] : '';
		$pdf_header_color        = ! empty( $pdf['pdf_header_color'] ) ? $pdf['pdf_header_color'] : '#fff';
		$pdf_header_bg_color     = ! empty( $pdf['pdf_header_bg_color'] ) ? $pdf['pdf_header_bg_color'] : '#382673';
		$pdf_footer_color        = ! empty( $pdf['pdf_footer_color'] ) ? $pdf['pdf_footer_color'] : '#fff';
		$pdf_footer_bg_color     = ! empty( $pdf['pdf_footer_bg_color'] ) ? $pdf['pdf_footer_bg_color'] : '#5D5676';
		$pdf_bg_upload_image     = ! empty( $pdf_bg_upload_image ) ? 'background-image: url("' . esc_attr( $pdf_bg_upload_image ) . '");' : '';
		$pdf_header_upload_image = ! empty( $pdf_header_upload_image ) ? '<img src="' . esc_attr( $pdf_header_upload_image ) . '" style="height: 60; max-width: 100%; ">' : '';

		$mpdf = new \Mpdf\Mpdf( [ 
			'fontdata' => [ // lowercase letters only in font key
				'dejavuserifcond' => [ 
					'R' => 'DejaVuSansCondensed.ttf',
				]
			],
			'mode' => 'utf-8',
			'default_font' => 'dejavusanscond',
			'margin_header' => 0,
			'margin_footer' => 0,
			'format' => 'A4',
			'margin_left' => 0,
			'margin_right' => 0,
			'margin_top' => 25
		] );


		// PDF Style
		$pdf_style = ' <style>
            body {
                 ' . esc_attr( $pdf_bg_upload_image ) . '
                background-repeat:no-repeat;
                background-image-resize: 6; 
				background-color: #F8F7FD;
            }
            .pdf-header{
                height: 60px;   
                background-color: ' . esc_attr( $pdf_header_bg_color ) . ';
                color : ' . esc_attr( $pdf_header_color ) . '; 
				padding: 10px 25px;
            }
			.pdf-header p{
				margin: 0;
			}
            .pdf-footer{ 
                background-color: ' . esc_attr( $pdf_footer_bg_color ) . ';
                color : ' . esc_attr( $pdf_footer_color ) . '; 
				padding: 10px 25px;
            }
            .pdf-content{ 
                background-color: ' . esc_attr( $pdf_content_bg_color ) . ';
                color : ' . esc_attr( $pdf_content_color ) . ';
                padding: 20px;
                height: 100%;
            }
            .pdf-content table{  
				width: 100%; 
				border-collapse: collapse; 
				border: 1px dashed rgb(227, 227, 227) ;
				background-color: #F8F7FD;
				padding: 10px 15px;
				margin-top: 15px;
				border-radius: 10px !important;
			}
			.pdf-content tr {
				padding: 10px;
				margin-lef: 10px;
				margin-right: 10px;
			}
			
			.pdf-content tr td{ 
				padding-left: 15px;
				text-align: left;
				border-bottom: 1px solid #F0EEF6;
				display: flex;
				justify-content: flex-start;
				align-items: center;
			} 
			pdf-content tr td:first-child{
				min-width: 25%;
			}
			.pdf-content tr:last-child td{
				border-bottom: 0;
			}
            .header-logo{
				text-align: ' . esc_attr( $pdf_header_img_aline ) . '; 
				width: 20%;
				float: left;
			}
			.header-content{ 
				float: right;
				width: 80%;
				text-align: right;
			}
            ' . $custom_pdf_css . '
        </style>';

		// PDF Header checked( 'on', $disable_header );
		if ( $disable_header != true ) {
			$mpdf->SetHTMLHeader( '
            <div class="pdf-header"  >
                    <div class="header-logo">
                        ' . $pdf_header_upload_image . '
                    </div>    
                    <div class="header-content">
                    ' .  $customize_pdf_header . '
                    </div>
            </div>
            ' );
		}


		// PDF Footer
		if ( $disable_footer != true ) {
			$mpdf->SetHTMLFooter( '<div class="pdf-footer">' . $customize_pdf_footer . '</div>' );
		}

		$replace_key = [];
		$repeaters = [];
		$repeater_value = [];
		$replace_value = [];
		$uploaded_files = [];

		// Call UACF7_DATABASE Class
		$uacf7_DB = null;
		$ContactForm = WPCF7_ContactForm::get_instance( $form_id );
		$form_fields = $ContactForm->scan_form_tags();

		$encryptionKey = 'AES-256-CBC';
		$uacf7_signature_tag = [];
		$uacf7_file_tag = [];

		if ( class_exists( 'UACF7_DATABASE' ) ) {
			$uacf7_DB = new UACF7_DATABASE();
		}

		// Get and store all uacf7_signature tags 
		foreach ( $form_fields as $field ) {
			if ( $field->type == 'uacf7_signature*' || $field->type == 'uacf7_signature' ) {
				$uacf7_signature_tag[] = $field->name;
			}
		}

		// Get and store all CF7 File tags 
		foreach ( $form_fields as $field ) {
			if ( $field->type == 'file*' || $field->type == 'file' ) {
				$uacf7_file_tag[] = $field->name;
			}
		}

		$form_value = json_decode( $data->form_value );
		foreach ( $form_value as $key => $value ) {
			// Repeater value gate
			if ( strpos( $key, '__' ) !== false ) {
				$name_parts = explode( '__', $key );
				if ( is_array( $name_parts ) ) {
					$repeater_value[ $name_parts[0] ][ $name_parts[1] ] = $name_parts[0];
				}
			}

			if ( strpos( $key, "_count" ) !== false ) {
				$repeaters[] = str_replace( '_count', '', $key );
			}

			// Signature Image Decrypt form Database Addon
			if ( in_array( $key, $uacf7_signature_tag ) && $uacf7_DB != null ) {
				$pathInfo = pathinfo( $value );
				$extension = strtolower( $pathInfo['extension'] );

				ob_start();
				echo $uacf7_DB->decrypt_and_display( $dir . $value, $encryptionKey );
				$decryptedData = ob_get_clean();

				if ( $decryptedData !== null ) {
					$value = 'data:image/png;base64,' . base64_encode( $decryptedData );
				}
			}

			//File tags URL set in the value
			if ( in_array( $key, $uacf7_file_tag ) ) {
				$value = $url . $value;
			}

			$replace_key[] = '[' . $key . ']';
			if ( is_array( $value ) ) {
				$data = '';
				$count_value = count( $value );
				for ( $x = 0; $x < $count_value; $x++ ) {
					if ( $x == 0 ) {
						$data .= $value[ $x ];
					} else {
						$data .= ', ' . $value[ $x ];
					}

				}
				$value = $data;
			}
			$replace_value[] = $value;
		}
		// Repeater value
		if ( ! empty( $repeaters ) && is_array( $repeaters ) ) {
			$repeater_data = apply_filters( 'uacf7_pdf_generator_replace_data', $repeater_value, $repeaters, $customize_pdf );
			$customize_pdf = str_replace( $repeater_data['replace_re_key'], $repeater_data['replace_re_value'], $customize_pdf );
		}


		$pdf_content = str_replace( $replace_key, $replace_value, $customize_pdf );
		$pdf_content = apply_filters( 'uacf7_pdf_generator_replace_condition_data', $pdf_content, $form_id, $form_value );

		$mpdf->SetTitle( $uacf7_pdf_name );

		$mpdf->WriteHTML( $pdf_style, \Mpdf\HTMLParserMode::HEADER_CSS );

		// PDF Footer Content
		$mpdf->WriteHTML( '<div class="pdf-content">' . nl2br( $this->makeLinksClickable($pdf_content) ) . '   </div>' );

		// make directory 
		if ( ! file_exists( $dir . '/uacf7-uploads' ) ) {
			wp_mkdir_p( $dir . '/uacf7-uploads' );
		}
		$pdf_dir = $dir . '/uacf7-uploads/' . $uacf7_pdf_name . '_db_.pdf';
		$pdf_url = $url . '/uacf7-uploads/' . $uacf7_pdf_name . '_db_.pdf';

		$mpdf->Output( $pdf_dir, 'F' ); // Dwonload

		wp_send_json(
			array(
				'status' => 'success',
				'url' => $pdf_url
			)
		);

		die();
	}

	function uacf7_wpcf7_mail_components( $components, $form = null, $mail = null ) {


		$wpcf7 = WPCF7_ContactForm::get_current();

		// Pdf get Meta Option
		$pdf = uacf7_get_form_option( $wpcf7->id(), 'pdf_generator' );

		$enable_pdf = isset( $pdf['uacf7_enable_pdf_generator'] ) ? $pdf['uacf7_enable_pdf_generator'] : 0;
        $enable_pdf_download = isset( $pdf['uacf7_enable_pdf_form_download'] ) ? $pdf['uacf7_enable_pdf_form_download'] : 0;

		$pdf_send_to = isset( $pdf['pdf_send_to'] ) ? $pdf['pdf_send_to'] : '';
		if ( ( $pdf_send_to == 'mail-1' && $mail->name() == 'mail_2' ) || ( $pdf_send_to == 'mail-2' && $mail->name() == 'mail' ) ) {
			return $components;
		}
		if ( $enable_pdf == true ) {
			$submission = WPCF7_Submission::get_instance();
			$contact_form_data = $submission->get_posted_data();
			$files = $submission->uploaded_files();

			require UACF7_PATH . 'third-party/vendor/autoload.php';
			$upload_dir = wp_upload_dir();
			$time_now = time();
			$dir = $upload_dir['basedir'];
			$upload_dir = wp_upload_dir();
			$url = $upload_dir['baseurl'];
			$uploaded_files = [];
			$uacf7_dirname = $upload_dir['basedir'] . '/uacf7-uploads';
			if ( ! file_exists( $uacf7_dirname ) ) {
				wp_mkdir_p( $uacf7_dirname );
			}
			foreach ( $_FILES as $file_key => $file ) {
				array_push( $uploaded_files, $file_key );
			}

			//  
			$uacf7_pdf_name = ! empty( $pdf['uacf7_pdf_name'] ) ? $pdf['uacf7_pdf_name'] : get_the_title( $wpcf7->id() );
			$disable_header = ! empty( $pdf['uacf7_pdf_disable_header_footer'] ) && in_array( 'header', $pdf['uacf7_pdf_disable_header_footer'] ) ? true : false;
			$disable_footer = ! empty( $pdf['uacf7_pdf_disable_header_footer'] ) && in_array( 'footer', $pdf['uacf7_pdf_disable_header_footer'] ) ? true : false;
			$customize_pdf = ! empty( $pdf['customize_pdf'] ) ? $pdf['customize_pdf'] : '';
			$pdf_bg_upload_image = ! empty( $pdf['pdf_bg_upload_image'] ) ? $pdf['pdf_bg_upload_image'] : '';
			$customize_pdf_header = ! empty( $pdf['customize_pdf_header'] ) ? $pdf['customize_pdf_header'] : '';
			$pdf_header_upload_image = ! empty( $pdf['pdf_header_upload_image'] ) ? $pdf['pdf_header_upload_image'] : '';
			$pdf_header_img_height = ! empty( $pdf['pdf_header_img_height'] ) ? $pdf['pdf_header_img_height'] : '';
			$pdf_header_img_width = ! empty( $pdf['pdf_header_img_width'] ) ? $pdf['pdf_header_img_width'] : '';
			$pdf_header_img_aline = ! empty( $pdf['pdf_header_img_aline'] ) ? $pdf['pdf_header_img_aline'] : '';
			$customize_pdf_footer = ! empty( $pdf['customize_pdf_footer'] ) ? $pdf['customize_pdf_footer'] : '';
			$custom_pdf_css = ! empty( $pdf['custom_pdf_css'] ) ? $pdf['custom_pdf_css'] : '';
			$pdf_content_color = ! empty( $pdf['pdf_content_color'] ) ? $pdf['pdf_content_color'] : '#000';
			$pdf_content_bg_color = ! empty( $pdf['pdf_content_bg_color'] ) ? $pdf['pdf_content_bg_color'] : '';
			$pdf_header_color = ! empty( $pdf['pdf_header_color'] ) ? $pdf['pdf_header_color'] : '#fff';
			$pdf_header_bg_color = ! empty( $pdf['pdf_header_bg_color'] ) ? $pdf['pdf_header_bg_color'] : '#382673';
			$pdf_footer_color = ! empty( $pdf['pdf_footer_color'] ) ? $pdf['pdf_footer_color'] : '#fff';
			$pdf_footer_bg_color = ! empty( $pdf['pdf_footer_bg_color'] ) ? $pdf['pdf_footer_bg_color'] : '#5D5676';
			$pdf_bg_upload_image = ! empty( $pdf_bg_upload_image ) ? 'background-image: url("' . esc_attr( $pdf_bg_upload_image ) . '");' : '';
			$pdf_header_upload_image = ! empty( $pdf_header_upload_image ) ? '<img src="' . esc_attr( $pdf_header_upload_image ) . '" style="height: 60; max-width: 100%; ">' : '';
			
			$mpdf = new \Mpdf\Mpdf( [ 
				'fontdata' => [ // lowercase letters only in font key
					'dejavuserifcond' => [ 
						'R' => 'DejaVuSansCondensed.ttf',
					]
				],
				'mode' => 'utf-8',
				'default_font' => 'dejavusanscond',
				'margin_header' => 0,
				'margin_footer' => 0,
				'format' => 'A4',
				'margin_left' => 0,
				'margin_right' => 0,
				'margin_top' => 25,
			] );
			$replace_key = [];

			// PDF Style
			$pdf_style = ' <style>
                body {
                    background:url(' . $pdf_bg_upload_image . ');
                    background-repeat:no-repeat;
                    background-image-resize: 6; 
					background-color: #F8F7FD;
                }
                .pdf-header{
                    height: 60px;   
                    background-color: ' . esc_attr( $pdf_header_bg_color ) . ';
                    color : ' . esc_attr( $pdf_header_color ) . '; 
					padding: 10px 25px;
                }
				.pdf-header p{
					margin: 0;
				}
                .pdf-footer{ 
                    background-color: ' . esc_attr( $pdf_footer_bg_color ) . ';
                    color : ' . esc_attr( $pdf_footer_color ) . '; 
					padding: 10px 25px;
                }
                .pdf-content{ 
                    background-color: ' . esc_attr( $pdf_content_bg_color ) . ';
                    color : ' . esc_attr( $pdf_content_color ) . ';
                    padding: 20px;
                    height: 100%;
                }
                .pdf-content table{  
                    width: 100%; 
                    border-collapse: collapse; 
                    border: 1px dashed rgb(227, 227, 227) ;
					background-color: #F8F7FD;
					padding: 10px 15px;
					margin-top: 15px;
					border-radius: 10px !important;
                }
				.pdf-content tr {
					padding: 10px;
					margin-lef: 10px;
					margin-right: 10px;
				}
				
                .pdf-content tr td{ 
                    padding-left: 15px;
                    text-align: left;
					border-bottom: 1px solid #F0EEF6;
					display: table-cell !important;  
    				vertical-align: middle !important;
                } 
				pdf-content tr td:first-child{
					min-width: 25%;
				}
				.pdf-content tr:last-child td{
					border-bottom: 0;
				}
                .header-logo{
                    text-align: ' . esc_attr( $pdf_header_img_aline ) . '; 
                    width: 20%;
					float: left;
                }
                .header-content{ 
					float: right;
                    width: 80%;
					text-align: right;
                }
                ' . $custom_pdf_css . '
            </style>';
			$replace_value = [];

			// PDF Header
			if ( $disable_header != true ) {
				$mpdf->SetHTMLHeader( '
                <div class="pdf-header"  >
                        <div class="header-logo"  >
                            ' . $pdf_header_upload_image . '
                        </div>    
                        <div class="header-content">
                        ' . $customize_pdf_header . '
                        </div>
                </div>
                ' );
			}

			// PDF Footer
			if ( $disable_footer != true ) {
				$mpdf->SetHTMLFooter( '<div class="pdf-footer">' . $customize_pdf_footer . '</div>' );
			}

			$repeater_value = [];
			foreach ( $contact_form_data as $key => $value ) {
				if ( ! in_array( $key, $uploaded_files ) ) {
					$replace_key[] = '[' . $key . ']';

					// Repeater value gate
					if ( strpos( $key, '__' ) !== false ) {
						$name_parts = explode( '__', $key );
						if ( is_array( $name_parts ) ) {
							$repeater_value[ $name_parts[0] ][ $name_parts[1] ] = $name_parts[0];
						}
					}

					if ( is_array( $value ) ) {

						$data = '';
						$count_value = count( $value );
						for ( $x = 0; $x < $count_value; $x++ ) {
							if ( $x == 0 ) {
								$data .= $value[ $x ];
							} else {
								$data .= ', ' . $value[ $x ];
							}

						}
						$value = $data;
					}
					$replace_value[] = $value;
				}

			}
			foreach ( $files as $file_key => $file ) {
				if ( ! empty( $file ) ) {
					if ( in_array( $file_key, $uploaded_files ) ) {
						$file = is_array( $file ) ? reset( $file ) : $file;
						$dir_link = '/uacf7-uploads/' . $time_now . '-' . $file_key . '-' . basename( $file );
						copy( $file, $dir . $dir_link );
						$replace_key[] = '[' . $file_key . ']';
						$replace_value[] = $upload_dir['baseurl'] . $dir_link;
					}
				}

			}

			// Repeater value
			if ( isset( $_POST['_uacf7_repeaters'] ) ) {
				$repeaters = json_decode( stripslashes( $_POST['_uacf7_repeaters'] ) );

				if ( isset( $repeaters ) || is_array( $repeaters ) ) {
					$repeater_data = apply_filters( 'uacf7_pdf_generator_replace_data', $repeater_value, $repeaters, $customize_pdf );

					$customize_pdf = str_replace( $repeater_data['replace_re_key'], $repeater_data['replace_re_value'], $customize_pdf );
				}
			}

			$pdf_content = str_replace( $replace_key, $replace_value, $customize_pdf );
			// Replace extranal data using this content;

			$pdf_content = apply_filters( 'uacf7_pdf_generator_replace_condition_data', $pdf_content, $wpcf7->id(), $contact_form_data );

			// Replace PDF Name
			$uacf7_pdf_name = str_replace( $replace_key, $replace_value, $uacf7_pdf_name );

			$mpdf->SetTitle( $uacf7_pdf_name );

			$mpdf->WriteHTML( $pdf_style, \Mpdf\HTMLParserMode::HEADER_CSS );
			
			// PDF Footer Content
			$mpdf->WriteHTML( '<div class="pdf-content">' . nl2br( $pdf_content) . '   </div>' );

			$pdf_dir = $dir . '/uacf7-uploads/' . $uacf7_pdf_name . '.pdf';
			$pdf_url = $url . '/uacf7-uploads/' . $uacf7_pdf_name . '.pdf';

			$mpdf->Output( $pdf_dir, 'F' ); 

			if($enable_pdf_download){
				// Store the PDF URL temporarily (transient or global variable)
				set_transient('uacf7_pdf_url_' . $wpcf7->id(), $pdf_url, 60);
			}

			$components['attachments'][] = $pdf_dir;
		}
		return $components;

	}

	public function makeLinksClickable($text) {
		$pattern = '/(https?:\/\/[^\s]+)/';
		
		return preg_replace($pattern, '<a href="$1">$1</a>', $text);
	}


	// Import Export 
	public function uacf7_post_meta_import_export_pdf_generator( $imported_data, $form_id ) {
		if ( isset( $imported_data['pdf_generator'] ) && function_exists( 'uacf7_import_export_file_upload' ) ) {
			$imported_data['pdf_generator']['pdf_bg_upload_image'] = uacf7_import_export_file_upload( $imported_data['pdf_generator']['pdf_bg_upload_image'] );
		}
		if ( isset( $imported_data['pdf_generator'] ) && function_exists( 'uacf7_import_export_file_upload' ) ) {
			$imported_data['pdf_generator']['pdf_header_upload_image'] = uacf7_import_export_file_upload( $imported_data['pdf_generator']['pdf_header_upload_image'] );
		}
		return $imported_data;
	}


}

new UACF7_PDF_GENERATOR();
