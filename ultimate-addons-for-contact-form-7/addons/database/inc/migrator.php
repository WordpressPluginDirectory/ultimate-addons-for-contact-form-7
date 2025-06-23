<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class UACF7_DBMigrator {


	public function __construct() {
	}

	public function uacf7dp_check_free_db() {
		global $wpdb;
		$Saved_form_data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "uacf7_form" );
		$ExtraFields = [];

		if ( ! empty( $Saved_form_data ) ) {

			// Delete all data from wp_uacf7dp_data
			$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}uacf7dp_data" );

			// Delete all data from wp_uacf7dp_data_entry
			$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}uacf7dp_data_entry" );

			$getting_old_from_entrys = [];

			foreach ( $Saved_form_data as $form_key => $form_data ) {
				$old_subform_input_data = $form_data->form_value;
				$form_cf7_id = $form_data->form_id;

				// Making extra fields
				$ExtraFields['submit_ip'] = null;
				$ExtraFields['submit_time'] = $form_data->form_date;
				$ExtraFields['cf7_form_id'] = $form_cf7_id;

				// Convert the object to an array
				$old_subform_input_data = json_decode( $old_subform_input_data );
				$old_subform_input_data_array = is_array( $old_subform_input_data ) ? $old_subform_input_data : json_decode( json_encode( $old_subform_input_data ), true );


				$org_old_subform_data = array_merge( $old_subform_input_data_array, $ExtraFields );
				$getting_old_from_entrys[] = $org_old_subform_data;
			}

			$this->uacf7dp_get_form_data_migrat( $getting_old_from_entrys );
		}


	}

	public function uacf7dp_get_form_data_migrat( $insert_data ) {
		global $wpdb;
		$Get_all_form_entry = $insert_data;

		// Insert data to the pro data table 
		foreach ( $Get_all_form_entry as $form_key => $data ) {
			$submit_form_id = $data['cf7_form_id'];
			$submit_ip = $data['submit_ip'];
			$submit_time = $data['submit_time'];

			$wpdb->query( $wpdb->prepare( 'INSERT INTO ' . $wpdb->prefix . 'uacf7dp_data(`cf7_form_id`, `submit_ip`, `submit_time`) VALUES (%d, %d, %s)', $submit_form_id, $submit_ip, $submit_time ) );
			$data_id = $wpdb->insert_id;

			$uacf7dp_no_save_fields = uacf7dp_no_save_fields();

			foreach ( $data as $k => $v ) {
				if ( in_array( $k, $uacf7dp_no_save_fields ) ) {
					continue;
				} else {
					if ( is_array( $v ) ) {
						$v = implode( "\n", $v );
					}
					$wpdb->query( $wpdb->prepare( 'INSERT INTO ' . $wpdb->prefix . 'uacf7dp_data_entry(`cf7_form_id`, `data_id`, `fields_name`, `value`) VALUES (%d,%d,%s,%s)', $submit_form_id, $data_id, $k, $v ) );
				}
			}

		}
	}
}
