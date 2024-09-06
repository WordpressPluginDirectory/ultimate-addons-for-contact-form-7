<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


class UACF7_TELEGRAM {

	public $id;

	public function __construct() {

		require_once 'inc/telegram.php';

		add_action( 'wpcf7_before_send_mail', [ $this, 'uacf7_send_contact_form_data_to_telegram' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'uacf7_telegram_admin_js_script' ] );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_telegram' ), 21, 2 );

	}


	public function uacf7_telegram_admin_js_script() {

		wp_enqueue_script( 'uacf7-telegram-scripts', UACF7_ADDONS . '/telegram/assets/js/admin-script.js', [ 'jquery' ], 'UACF7_VERSION', true );


	}


	public function uacf7_post_meta_options_telegram( $value, $post_id ) {

		$telegram = apply_filters( 'uacf7_post_meta_options_telegram_pro', $data = array(
			'title' => __( 'Telegram', 'ultimate-addons-cf7' ),
			'icon' => 'fa-brands fa-telegram',
			'checked_field' => 'uacf7_telegram_enable',
			'fields' => array(

				'uacf7_telegram_heading' => array(
					'id' => 'uacf7_telegram_heading',
					'type' => 'heading',
					'label' => __( 'Telegram Integration', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Forward form submission data to Telegram automatically. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/contact-form-7-telegram/" target="_blank" rel="noopener">Example</a>'
					)
				),

				'telegram_docs' => array(
					'id' => 'telegram_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-telegram/" target="_blank" rel="noopener">Telegram Integration</a>'
					)
				),
				'uacf7_telegram_enable' => array(
					'id' => 'uacf7_telegram_enable',
					'type' => 'switch',
					'label' => __( ' Enable Telegram Integration', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false,
					'field_width' => 50,
				),
				'uacf7_telegram_form_options_heading' => array(
					'id' => 'uacf7_telegram_form_options_heading',
					'type' => 'heading',
					'label' => __( 'Telegram Option ', 'ultimate-addons-cf7' ),
				),
				'uacf7_telegram_enable_icon' => array(
					'id' => 'uacf7_telegram_enable_icon',
					'type' => 'callback',
					'function' => 'uacf7_telegram_active_status_callback',
					'argument' => $post_id,

				),
				'uacf7_telegram_bot_token' => array(
					'id' => 'uacf7_telegram_bot_token',
					'type' => 'text',
					'label' => __( ' Telegram BOT Token ', 'ultimate-addons-cf7' ),
					'placeholder' => __( ' Paste here Telegram BOT TOKEN..... ', 'ultimate-addons-cf7' ),
					'description' => __( '<a target="_blank" href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-telegram/#creating-a-bot-with-botfather">Click here</a> to learn how to get BOT Token.', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_telegram_chat_id' => array(
					'id' => 'uacf7_telegram_chat_id',
					'type' => 'text',
					'label' => __( ' Telegram Chat ID ', 'ultimate-addons-cf7' ),
					'placeholder' => __( ' Paste here Telegram Chat ID..... ', 'ultimate-addons-cf7' ),
					'description' => __( '<a target="_blank" href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-telegram/#getting-the-user-chat-id">Click here</a> to learn how to get Chat ID.', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),

			),


		), $post_id );

		$value['telegram'] = $telegram;
		return $value;
	}

	public function uacf7_send_contact_form_data_to_telegram( $contact_form ) {

		$submission = WPCF7_Submission::get_instance();
		if ( $submission ) {
			$form_id = $contact_form->id();
			$form_name = $contact_form->title();


			$posted_data = $submission->get_posted_data();

			$form_tags = $submission->get_contact_form()->scan_form_tags();

			$properties = $submission->get_contact_form()->get_properties();

			$mail = $contact_form->prop( 'mail' );
			$message = wpcf7_mail_replace_tags( @$mail['body'] );

			$this->uacf7_send_message_to_telegram( $message, $form_id );

		}


	}


	public function uacf7_send_message_to_telegram( $message, $form_id ) {
		/**
		 * Getting Bot Token & Chat ID from the Database
		 */

		$uacf7_telegram_settings = uacf7_get_form_option( $form_id, 'telegram' );


		if ( ! empty( $uacf7_telegram_settings ) ) {
			$uacf7_telegram_enable = $uacf7_telegram_settings['uacf7_telegram_enable'];
			$uacf7_telegram_bot_token = $uacf7_telegram_settings['uacf7_telegram_bot_token'];
			$uacf7_telegram_chat_id = $uacf7_telegram_settings['uacf7_telegram_chat_id'];

		}



		$uacf7_telegram_enable = isset( $uacf7_telegram_enable ) ? $uacf7_telegram_enable : '0';
		$bot_token = isset( $uacf7_telegram_bot_token ) ? $uacf7_telegram_bot_token : '';
		$chat_id = isset( $uacf7_telegram_chat_id ) ? $uacf7_telegram_chat_id : '';

		if ( $uacf7_telegram_enable === '1' && ! empty( $bot_token ) && ! empty( $chat_id ) ) {
			$api_url = "https://api.telegram.org/bot$bot_token/sendMessage";

			$args = array(
				'chat_id' => $chat_id,
				'text' => $message,
			);


			$response = wp_remote_post( $api_url, array(
				'body' => json_encode( $args ),
				'headers' => array( 'Content-Type' => 'application/json' ),
			) );


			if ( is_wp_error( $response ) ) {
				error_log( 'Telegram API request failed: ' . $response->get_error_message() );
			}
		}

	}

}


$UACF7_TELEGRAM = new UACF7_TELEGRAM();


