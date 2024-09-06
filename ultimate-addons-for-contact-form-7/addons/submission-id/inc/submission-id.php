<?php

// Do not access directly

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_SUBMISSION_ID_PANEL {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'uacf7_create_submission_id_database_col' ] );
		add_filter( 'tf_metabox_before_save_option', [ $this, 'tf_metabox_before_save_option_submission_id_callback' ], 10, 2 );

	}



	/**
	 * Before Save Metabox Action
	 */
	public function tf_metabox_before_save_option_submission_id_callback( $value, $form_id ) {

		$submission = WPCF7_Submission::get_instance();
		$submission_data = $value['submission_id'];
		// exit;
		if ( $submission_data['uacf7_submission_id'] < 0 || $submission_data['uacf7_submission_id'] === null || $submission_data['uacf7_submission_id'] === '' ) {
			$initial_value = 1;
			$value['submission_id']['uacf7_submission_id'] = $initial_value;
		} else {

			global $wpdb;
			$table_name = $wpdb->prefix . 'uacf7_form';
			$last_item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM $table_name WHERE form_id= %d  ORDER BY submission_id DESC ", $form_id )
			);


			/** Submission ID Conditional Update */
			if ( $last_item !== null && $last_item->submission_id != 0 ) {
				$default_step = $submission_data['uacf7_submission_id_step'] != '' ? $submission_data['uacf7_submission_id_step'] : 1;

				if ( isset( $submission_data['uacf7_submission_id'] ) && $submission_data['uacf7_submission_id'] > $last_item->submission_id ) {
					$value['submission_id']['uacf7_submission_id'] = $submission_data['uacf7_submission_id'];
					// update_post_meta( $form->id(), 'uacf7_submission_id', sanitize_text_field($submission_data['uacf7_submission_id']) );
				} else {
					$value['submission_id']['uacf7_submission_id'] = $last_item->submission_id + intval( $default_step );
					// update_post_meta( $form->id(), 'uacf7_submission_id', sanitize_text_field($last_item->submission_id + intval($default_step))  );
				}
			} else {
				$value['submission_id']['uacf7_submission_id'] = $submission_data['uacf7_submission_id'];
				// update_post_meta( $form->id(), 'uacf7_submission_id', sanitize_text_field($submission_data['uacf7_submission_id']) );
			}

		}

		return $value;

	}



	/**
	 * Create a Database column named "submission_id"
	 */
	public function uacf7_create_submission_id_database_col() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'uacf7_form';
		$table_exist = $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" );

		if ( $table_exist == $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$tableName = $wpdb->prefix . 'leaguemanager_person_status';
			$sql_checked = "SELECT *  FROM information_schema.COLUMNS  WHERE 
                              TABLE_SCHEMA = '$wpdb->dbname' 
                          AND TABLE_NAME = '$table_name' 
                          AND COLUMN_NAME = 'submission_id'";

			$checked_status = $wpdb->query( $sql_checked );
			if ( $checked_status != true ) {
				$sql = "ALTER TABLE $table_name 
        MODIFY COLUMN form_date DATETIME NULL,
        ADD submission_id bigint(20) DEFAULT 0 NULL AFTER form_value";
				$wpdb->query( $sql );
			}
		}

	}

}
new UACF7_SUBMISSION_ID_PANEL();

