<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_WEB_HOOK {

	/*
	 * Construct function
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_webhook_style' ) );

		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_webhook' ), 12, 2 );

		add_action( 'wpcf7_before_send_mail', array( $this, 'uacf7_send_data_by_web_hook' ) );
		// add_filter( 'wpcf7_load_js', '__return_false' );
	}


	public function enqueue_webhook_style() {
		wp_enqueue_style( 'uacf7-web-hook', UACF7_ADDONS . '/web-hook/css/web-hook.css' );
		wp_enqueue_script( 'uacf7-web-hook-script', UACF7_ADDONS . '/web-hook/js/web-hook.js', array( 'jquery' ), '', true );
	}

	// Add Web Hook Options
	public function uacf7_post_meta_options_webhook( $value, $post_id ) {

		$WebHook = apply_filters( 'uacf7_post_meta_options_webhook', $data = array(
			'title' => __( 'Webhook', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-code-compare',
			'checked_field'   => 'uacf7_enable_web_hook',
			'fields' => [ 
				'uacf7_Web_hook_heading' => [ 
					'id' => 'uacf7_web_hook_heading',
					'type' => 'heading',
					'label' => __( 'Webhook (Pabbly/Zapier) Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
                        __( 'Transfer form data to third-party services like Pabbly or Zapier via webhooks. See Demo %1s.', 'ultimate-addons-cf7' ),
                         '<a href="#" target="_blank">Example</a>'
                    )
				],
				'webhook_docs' => [ 
					'id'      => 'webhook_docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => sprintf( 
                        __( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
                        '<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-webhook/" target="_blank">Webhook Setup</a>'
                    )
				],
				'uacf7_enable_web_hook' => [ 
					'id' => 'uacf7_enable_web_hook',
					'type' => 'switch',
					'label' => __( ' Enable Webhook ', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false
				],
				'web_hook_form_options_heading' => array(
					'id'        => 'web_hook_form_options_heading',
					'type'      => 'heading',
					'label'     => __( 'Webhook Option ', 'ultimate-addons-cf7' ),
				),

				'uacf7_web_hook_api' => [ 
					'id' => 'uacf7_web_hook_api',
					'type' => 'text',
					'label' => __( 'Request URL', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter a Request URL', 'ultimate-addons-cf7' ),
					'dependency' => [ 'uacf7_enable_web_hook', '==', 1 ],
				],

				'uacf7_web_hook_request_method' => [ 
					'id' => 'uacf7_web_hook_request_method',
					'type' => 'select',
					'label' => __( 'Request Method', 'ultimate-addons-cf7' ),
					'options' => array(
						'GET' => 'GET',
						'POST' => 'POST',
						'PUT' => 'PUT',
						'DELETE' => 'DELETE',
						'PATCH' => 'PATCH',
					),
					'field_width' => 50,
					'dependency' => [ 'uacf7_enable_web_hook', '==', 1 ],
				],

				'uacf7_web_hook_request_format' => [ 
					'id' => 'uacf7_web_hook_request_format',
					'type' => 'select',
					'label' => __( 'Request Format', 'ultimate-addons-cf7' ),
					'options' => array(
						'json' => 'JSON',
						'formdata' => 'FORMDATA',
					),
					'field_width' => 50,
					'dependency' => [ 'uacf7_enable_web_hook', '==', 1 ],
				],

				'uacf7_web_hook_header_request' => [ 
					'id' => 'uacf7_web_hook_header_request',
					'type' => 'repeater',
					'label' => __( 'Request Headers', 'ultimate-addons-cf7' ),
					'dependency' => [ 'uacf7_enable_web_hook', '==', 1 ],
					'fields' => [ 

						'uacf7_web_hook_header_request_custom' => [ 
							'id' => 'uacf7_web_hook_header_request_custom',
							'type' => 'radio',
							'class' => 'padding-bottom0',
							'label' => __( 'Enable Custom Value', 'ultimate-addons-cf7' ),
							'options' => [ 
								'form' => 'Form Data',
								'custom' => 'Custom Value',
							],
							'default' => 'form',
							'inline' => true,
						],

						'uacf7_web_hook_header_request_value' => [ 
							'id' => 'uacf7_web_hook_header_request_value',
							'class' => 'padding-top0',
							'type' => 'text',
							'placeholder' => __( 'Enter a parameter key', 'ultimate-addons-cf7' ),
							'field_width' => 50,
						],

						'uacf7_web_hook_header_request_parameter' => [ 
							'id' => 'uacf7_web_hook_header_request_parameter',
							'class' => 'padding-top0',
							'type' => 'select',
							// 'label' => __( 'Request Format', 'ultimate-addons-cf7' ),
							'options' => 'uacf7',
							'query_args' => array(
								'post_id' => $post_id,
								'exclude' => [ 'submit', 'conditional' ],
							),
							'dependency' => array( 'uacf7_web_hook_header_request_custom', '==', 'form' ),
							'field_width' => 50,
						],

						'uacf7_web_hook_header_request_parameter_custom' => [ 
							'id' => 'uacf7_web_hook_header_request_parameter_custom',
							'class' => 'padding-top0',
							'type' => 'text',
							'placeholder' => __( 'Custom value', 'ultimate-addons-cf7' ),
							// 'label' => __( 'Request Format', 'ultimate-addons-cf7' ),
							'dependency' => array( 'uacf7_web_hook_header_request_custom', '==', 'custom' ),
							'field_width' => 50,
						],
					]

				],

				'uacf7_web_hook_body_request' => [ 
					'id' => 'uacf7_web_hook_body_request',
					'type' => 'repeater',
					'label' => __( 'Request Body', 'ultimate-addons-cf7' ),
					'dependency' => [ 'uacf7_enable_web_hook', '==', 1 ],
					'fields' => [ 
						'uacf7_web_hook_body_request_value' => [ 
							'id' => 'uacf7_web_hook_body_request_value',
							'type' => 'text',
							'placeholder' => __( 'Enter a parameter key', 'ultimate-addons-cf7' ),
							'field_width' => 50,
						],

						'uacf7_web_hook_body_request_parameter' => [ 
							'id' => 'uacf7_web_hook_body_request_parameter',
							'type' => 'select',
							// 'label' => __( 'Request Format', 'ultimate-addons-cf7' ),
							'options' => 'uacf7',
							'query_args' => array(
								'post_id' => $post_id,
								'exclude' => [ 'submit', 'conditional' ],
							),
							'field_width' => 50,
						],
					]

				]
			],
		), $post_id );

		$value['Web_hook'] = $WebHook;
		return $value;
	}

	public function uacf7_send_data_by_web_hook( $form ) {

		$submission = WPCF7_Submission::get_instance();
		$contact_form_data = $submission->get_posted_data();
		$Web_hook = uacf7_get_form_option( $form->id(), 'Web_hook' );


		//Admin Option
		$web_hook_enable = isset( $Web_hook['uacf7_enable_web_hook'] ) ? $Web_hook['uacf7_enable_web_hook'] : false;
		$request_api = isset( $Web_hook['uacf7_web_hook_api'] ) ? $Web_hook['uacf7_web_hook_api'] : '';
		$request_method = isset( $Web_hook['uacf7_web_hook_request_method'] ) ? $Web_hook['uacf7_web_hook_request_method'] : '';
		$request_format = isset( $Web_hook['uacf7_web_hook_request_format'] ) ? $Web_hook['uacf7_web_hook_request_format'] : '';
		$header_request = isset( $Web_hook['uacf7_web_hook_header_request'] ) ? $Web_hook['uacf7_web_hook_header_request'] : '';
		$body_request = isset( $Web_hook['uacf7_web_hook_body_request'] ) ? $Web_hook['uacf7_web_hook_body_request'] : '';

		$api_endpoint = $request_api;
		$api_request_method = $request_method;

		// Return if not enable
		if ( ! $web_hook_enable ) {
			return;
		}
		// Return API Not Fill
		if ( empty( $api_endpoint ) ) {
			return;
		}
		// Return If post type not seleted
		if ( empty( $api_request_method ) ) {
			return;
		}

		// Define the data to send in the POST request
		$header_data = array();
		$body_data = array();


		// Check if $header_request is an array
		if ( is_array( $header_request ) ) {
			// Loop through each item in the array
			foreach ( $header_request as $header ) {
				// Access individual values using keys
				if ( $header['uacf7_web_hook_header_request_custom'] === 'custom' ) {
					$customKey = $header['uacf7_web_hook_header_request_parameter_custom'];
					if ( isset( $customKey ) ) {
						$header_value = $header['uacf7_web_hook_header_request_value'];
						$header_parameter = $customKey;
					}
				} else {
					$header_value = $header['uacf7_web_hook_header_request_value'];
					$header_parameter = $contact_form_data[ $header['uacf7_web_hook_header_request_parameter'] ];
				}
				// Add data to the $post_data array
				$header_data[ $header_value ] = $header_parameter;
			}
		}

		// Check if $body_request is an array
		if ( is_array( $body_request ) ) {
			// Loop through each item in the array
			foreach ( $body_request as $body ) {
				// Access individual values using keys
				$body_value = $body['uacf7_web_hook_body_request_value'];
				$body_parameter = $contact_form_data[ $body['uacf7_web_hook_body_request_parameter'] ];

				// Add data to the $body_data array
				$body_data[ $body_value ] = $body_parameter;
			}
		}

		// Set up the request arguments
		$request_args = array(
			'body' => json_encode( $body_data ),
			'headers' => array_merge(
				//Need loop for additional input
				[ 'Content-Type' => 'application/json' ],
				$header_data,
			),
			'method' => $api_request_method,
		);

		// Make the POST request
		$response = wp_remote_request( $api_endpoint, $request_args );

		// Check if the request was successful
		if ( is_wp_error( $response ) ) {
			// Handle error
			//echo 'Error: ' . $response->get_error_message();
		} else {
			// Request was successful, and $response contains the API response
			//$api_response = wp_remote_retrieve_body( $response );
			//echo 'API Response: ' . $api_response;
		}
	}

}
new UACF7_WEB_HOOK();