<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Pre Populate Classs
 */
class UACF7_DATABASE {

	/*
	 * Construct function
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_admin_script' ) );
		add_action( 'wpcf7_before_send_mail', array( $this, 'uacf7_save_to_database' ), 20, 4 );
		add_action( 'admin_menu', array( $this, 'uacf7_add_db_menu' ), 11, 2 );
		add_action( 'wp_ajax_uacf7_ajax_database_popup', array( $this, 'uacf7_ajax_database_popup' ) );
		add_action( 'wp_ajax_uacf7_ajax_database_export_csv', array( $this, 'uacf7_ajax_database_export_csv' ) );
		add_action( 'admin_init', array( $this, 'uacf7_create_database_table' ) );
		//add_filter( 'wpcf7_load_js', '__return_false' );

	}

	//Create Ulimate Database   
	function uacf7_create_database_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'uacf7_form';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id bigint(20) NOT NULL,
            form_value longtext NOT NULL,
            form_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/*
	 * Enqueue script Backend
	 */


	public function wp_enqueue_admin_script() {
		wp_enqueue_style( 'database-admin-style', UACF7_ADDONS . '/database/assets/css/database-admin.css' );
		wp_enqueue_script( 'database-admin', UACF7_ADDONS . '/database/assets/js/database-admin.js', array( 'jquery' ), null, true );
		wp_localize_script(
			'database-admin',
			'database_admin_url',
			array(
				'admin_url' => get_admin_url() . 'admin.php',
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'plugin_dir_url' => plugin_dir_url( __FILE__ ),
				'nonce' => wp_create_nonce( 'uacf7-form-database-admin-nonce' ),
			)
		);
	}

	/*
	 * Database menu 
	 */

	public function uacf7_add_db_menu() {

		add_submenu_page(
			'uacf7_settings',
			__( 'Database', 'ultimate-addons-cf7' ),
			__( 'Database', 'ultimate-addons-cf7' ),
			'manage_options',
			'ultimate-addons-db',
			apply_filters( 'uacf7_database_admin_page', array( $this, 'uacf7_create_database_page' ) ),
		);
	}

	/*
	 * Database Admin Page 
	 */

	public function uacf7_create_database_page() {

		$form_id = empty( $_GET['form_id'] ) ? 0 : (int) $_GET['form_id'];
		$pdf = empty( $_GET['pdf'] ) ? 0 : $_GET['pdf'];
		$data_id = empty( $_GET['data_id'] ) ? 0 : $_GET['data_id'];


		if ( ! empty( $form_id ) ) {
			$uacf7_ListTable = new uacf7_form_List_Table();
			$uacf7_ListTable->prepare_items();
			?>
			<div class="wrap">
				<div id="icon-users" class="icon32"></div>
				<h2>
					<?php echo esc_html__( 'Ultimate Database', 'ultimate-addons-cf7' ); ?>
				</h2>
				<?php settings_errors(); ?>
				<form method="post" action="">
					<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
					<?php $uacf7_ListTable->search_box( 'Search', 'search' ); ?>
					<?php $uacf7_ListTable->display(); ?>
				</form>
			</div>
			<section class="uacf7_popup_preview">
				<div class="uacf7_popup_preview_content">
					<div id="uacf7_popup_wrap">
						<div class="db_popup_view">
							<div class="close" title="Exit Full Screen">╳</div>
							<div id="db_view_wrap">
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php
		} else {

			global $wpdb;
			$list_forms = get_posts(
				array(
					'post_type' => 'wpcf7_contact_form',
					'posts_per_page' => -1
				)
			);
			?>

			<div class="wrap uacf7-admin-cont">
				<h1>
					<?php echo esc_html__( 'Ultimate Database Addon', 'ultimate-addons-cf7' ); ?>
				</h1>
				<p>
					<?php echo esc_html__( 'The Database addon helps store form data, view data in the admin backend, and export data in CSV format.', 'ultimate-addons-cf7' ); ?>
				</p>
				<br>
				<?php settings_errors(); ?>

				<!--Tab buttons start-->
				<div class="uacf7-tab">
					<a class="tablinks active" onclick="uacf7_settings_tab(event, 'uacf7_addons')">
						<?php echo esc_html__( 'Contact Form 7 Database', 'ultimate-addons-cf7' ); ?>
					</a>
				</div>
				<!--Tab buttons end-->

				<!--Tab Addons start-->
				<div id="uacf7_addons" class="uacf7-tabcontent" style="display:block">
					<table>
						<tr>
							<td>
								<h3>
									<?php echo esc_html__( 'Select Form :', 'ultimate-addons-cf7' ); ?>
									</h4>
							</td>
							<td>
								<select name="form-id" id="form-id">
									<option value="0">
										<?php echo esc_html__( 'Select Form', 'ultimate-addons-cf7' ); ?>
									</option>
									<?php
									foreach ( $list_forms as $form ) {
										$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . $wpdb->prefix . "uacf7_form WHERE form_id = %d", $form->ID ) );  // count number of data
										echo '<option value="' . esc_attr( $form->ID ) . '">' . esc_attr( $form->post_title ) . ' ( ' . $count . ' )</option>';
									}
									?>
								</select>
							</td>
							<td>
								<button type="submit" class="button-primary" id="database_submit">
									<?php echo esc_html__( 'Submit', 'ultimate-addons-cf7' ); ?>
								</button>
							</td>
						</tr>
					</table>
				</div>
				<!--Tab Addons end-->
			</div>
			<?php
		}
		// echo ob_get_clean();
	}

	public function encrypt_file( $inputFile, $outputFile, $key ) {
		$inputData = file_get_contents( $inputFile );

		// Generate an Initialization Vector (IV)
		$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );

		// Encrypt the data
		$encryptedData = openssl_encrypt( $inputData, 'aes-256-cbc', $key, 0, $iv );

		// Combine IV and encrypted data
		$encryptedFileContent = $iv . $encryptedData;

		// Save the encrypted content to the output file
		file_put_contents( $outputFile, $encryptedFileContent );
	}

	public function decrypt_and_display( $inputFile, $key ) {

		if ( ! file_exists( $inputFile ) ) {
			die( "Error: The file does not exist." );
		}

		// Read the encrypted content
		$encryptedFileContent = file_get_contents( $inputFile );

		if ( $encryptedFileContent === false ) {
			die( "Error: Unable to read file content." );
		}

		// Extract IV
		$ivSize = openssl_cipher_iv_length( 'aes-256-cbc' );
		$iv = substr( $encryptedFileContent, 0, $ivSize );

		// Extract encrypted data
		$encryptedData = substr( $encryptedFileContent, $ivSize );

		// Decrypt the data
		$decryptedData = openssl_decrypt( $encryptedData, 'aes-256-cbc', $key, 0, $iv );

		// Output the decrypted data directly
		//header( 'Content-Type: image/jpg' ); // Adjust content type based on your file type
		return $decryptedData;
	}

	/*
	 * Ultimate form save into the database
	 */
	public function uacf7_save_to_database( $form ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		if ( ! is_plugin_active( 'ultimate-addons-for-contact-form-7-pro/ultimate-addons-for-contact-form-7-pro.php' ) ) {
			if ( defined( 'UACF7_PRO_PATH_ADDONS' ) ) {
				require_once( UACF7_PRO_PATH_ADDONS . '/database-pro/functions.php' );
			} else {
				// Handle the case when the pro plugin is not active and the constant is not defined
				// You can add an error log, a fallback, or any other necessary action here
				// error_log( 'UACF7_PRO_PATH_ADDONS is not defined. Pro plugin might not be installed or active.' );
			}
		}
		global $wpdb;
		$encryptionKey = 'AES-256-CBC';
		$table_name = $wpdb->prefix . 'uacf7_form';

		$submission = WPCF7_Submission::get_instance();
		$ContactForm = WPCF7_ContactForm::get_instance( $form->id() );
		$tags = $ContactForm->scan_form_tags();
		$skip_tag_insert = [];
		$uacf7_signature_tag = [];
		foreach ( $tags as $tag ) {
			if ( $tag->type == 'uacf7_signature*' || $tag->type == 'uacf7_signature' ) {
				$uacf7_signature_tag[] = $tag->name;
			}
			if ( $tag->type == 'uacf7_step_start' || $tag->type == 'uacf7_step_end' || $tag->type == 'uarepeater' || $tag->type == 'conditional' || $tag->type == 'uacf7_conversational_start' || $tag->type == 'uacf7_conversational_end' ) {
				if ( $tag->name != '' ) {
					$skip_tag_insert[] = $tag->name;
				}
			}

		}

		$contact_form_data = $submission->get_posted_data();
		$files = $submission->uploaded_files();
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];
		$uploaded_files = [];
		$time_now = time();
		$data_file = [];
		$uacf7_dirname = $upload_dir['basedir'] . '/uacf7-uploads';
		if ( ! file_exists( $uacf7_dirname ) ) {
			wp_mkdir_p( $uacf7_dirname );
		}

		foreach ( $_FILES as $file_key => $file ) {
			array_push( $uploaded_files, $file_key );
		}

		// var_dump( $files );
		foreach ( $files as $file_key => $file ) {

			// var_dump( $file_key );

			if ( ! empty( $file ) ) {
				if ( in_array( $file_key, $uploaded_files ) ) {
					$file = is_array( $file ) ? reset( $file ) : $file;

					// var_dump( $file );

					$dir_link = '/uacf7-uploads/' . $time_now . '-' . $file_key;

					if ( in_array( $file_key, $uacf7_signature_tag ) ) {
						$dir_link = '/uacf7-uploads/' . $time_now . '-' . $file_key . '.enc';
						$this->encrypt_file( $file, $dir . $dir_link, $encryptionKey );
					} else {
						copy( $file, $dir . $dir_link );
					}
					array_push( $data_file, [ $file_key => $dir_link ] );
				}
			}

		}

		$key_count = 0;
		foreach ( $contact_form_data as $key => $value ) {
			if ( in_array( $key, $uploaded_files ) ) {
				if ( ! empty( $data_file ) && is_array( $data_file ) ) {
					$contact_form_data[ $key ] = $data_file[ $key_count ][ $key ];
				}
				$key_count++;
			}
		}


		$data = [ 
			'status' => 'unread',
		];
		$data = array_merge( $data, $contact_form_data );
		$insert_data = [];
		foreach ( $data as $key => $value ) {
			if ( ! in_array( $key, $skip_tag_insert ) ) {

				if ( is_array( $value ) ) {
					$insert_data[ $key ] = array_map( 'esc_html', $value );
				} else {
					$insert_data[ $key ] = esc_html( $value );
				}
			}
		}

		// Initialize the variable to avoid warnings
		$extra_fields_data = [];

		// Now, attempt to call the function if it exists
		if ( function_exists( 'uacf7dp_add_more_fields' ) ) {
			$extra_fields_data = uacf7dp_add_more_fields( $submission );
		}

		apply_filters( 'uacf7dp_send_form_data_before_insert', $insert_data, $extra_fields_data );

		$insert_data = json_encode( $insert_data );

		$wpdb->insert(
			$table_name,
			array(
				'form_id' => $form->id(),
				'form_value' => $insert_data,
				'form_date' => current_time( 'Y-m-d H:i:s' ),
			)
		);

		$uacf7_db_insert_id = $wpdb->insert_id;

		//  print_r($uacf7_enable_track_order);

		// Order tracking Action
		do_action( 'uacf7_checkout_order_traking', $uacf7_db_insert_id, $form->id() );

		// submission id Action
		do_action( 'uacf7_submission_id_insert', $uacf7_db_insert_id, $form->id(), $contact_form_data, $tags );

	}

	/*
	 * Ultimate form save into the database
	 */

	public function uacf7_ajax_database_popup() {

		// Capability check
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have permission to perform this action.' );
		}

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'uacf7-form-database-admin-nonce' ) ) {
			exit( esc_html__( "Security error", 'ultimate-addons-cf7' ) );
		}

		global $wpdb;
		$encryptionKey = 'AES-256-CBC';
		$id = intval( $_POST['id'] ); // data id
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['baseurl'];
		$replace_dir = '/uacf7-uploads/';
		$form_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "uacf7_form WHERE id = %d", $id ) );
		$ContactForm = WPCF7_ContactForm::get_instance( $form_data->form_id );
		$form_fields = $ContactForm->scan_form_tags();
		$data = json_decode( $form_data->form_value );

		$signaturepath = $upload_dir['basedir'];
		$uacf7_signature_tag = [];
		$fields = [];

		foreach ( $form_fields as $field ) {
			if ( $field['type'] != 'submit' && $field['type'] != 'uacf7_step_start' && $field['type'] != 'uacf7_step_end' && $field['type'] != 'uarepeater' && $field['type'] == 'uacf7_conversational_start' && $field['type'] == 'uacf7_conversational_end' ) {
				$fields[ $field['name'] ] = '';
			}
			if ( $field->type == 'uacf7_signature*' || $field->type == 'uacf7_signature' ) {
				$uacf7_signature_tag[] = $field->name;
			}
		}

		foreach ( $data as $key => $value ) {
			$fields[ $key ] = $value;
		}

		$html = '<div class="db-view-wrap"> 
                    <h3>' . get_the_title( $form_data->form_id ) . '</h3>
                    <span>' . esc_html( $form_data->form_date ) . '</span>
                    <table class="wp-list-table widefat fixed striped table-view-list">';
		$html .= '<tr> <th><strong>Fields</strong></th><th><strong>Values</strong> </th> </tr>';
		foreach ( $fields as $key => $value ) {
			if ( $key != 'status' ) {
				if ( is_array( $value ) ) {
					$value = implode( ", ", $value );
				}
				if ( in_array( $key, $uacf7_signature_tag ) ) {
					$pathInfo = pathinfo( $value );
					$extension = strtolower( $pathInfo['extension'] );
					$fileNameWithoutExtension = pathinfo( $value, PATHINFO_FILENAME );

					// Image Loaded
					$token = md5( uniqid() );
					$decryptedData = $this->decrypt_and_display( $signaturepath . $value, $encryptionKey );
					if ( $decryptedData !== null ) {
						$imageData = 'data:image/jpeg;base64,' . base64_encode( $decryptedData );
					}

					// Check old data
					// Check old data
					if ( $extension == 'enc' ) {
						$srcAttribute = $imageData;  // Set to empty or another value if needed
					} else {
						$srcAttribute = $value;
					}

					$html .= '
					<tr> 
						<td>
							<strong>' . esc_attr( $key ) . '</strong>
						</td> 
						<td>
							<button id="signature_view_btn">' . esc_html( 'View', 'ultimate-addons-cf7' ) . '</button>
							<a class="" href="' . $srcAttribute . '" download="' . $fileNameWithoutExtension . '">
								<button class="signature_bownload_btn">Download</button>
							</a>
						</td>
					</tr>
					<div class="signature_view_pops">
						<img class="signature_view_pops_img" src="' . $srcAttribute . '"/>
					</div>
					';


				} else {
					if ( strstr( $value, $replace_dir ) ) {
						$value = str_replace( $replace_dir, "", $value );
						$html .= '<tr> <td><strong>' . esc_attr( $key ) . '</strong></td> <td><a href="' . esc_url( $dir . $replace_dir . $value ) . '" target="_blank">' . esc_html( $value ) . '</a></td> </tr>';
					} else {
						$html .= '<tr> <td><strong>' . esc_attr( $key ) . '</strong></td> <td>' . esc_html( $value ) . '</td> </tr>';
					}
				}

			}
		}
		$html .= '</table></div>';

		// Update data as read
		if ( $data->status == 'unread' ) {
			$data->status = 'read';
			$data = json_encode( $data );
			$table_name = $wpdb->prefix . 'uacf7_form';
			$data = array(
				'form_value' => $data,
			);
			$where = array(
				'id' => $id
			);
			$wpdb->update( $table_name, $data, $where );
		}

		echo $html; // return all data

		wp_die();
	}

	/*
	 * Export CSV 
	 */

	public function uacf7_ajax_database_export_csv() {
		// Capability check
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have permission to perform this action.' );
		}

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'uacf7-form-database-admin-nonce' ) ) {
			exit( esc_html__( "Security error", 'ultimate-addons-cf7' ) );
		}

		if ( isset( $_POST['form_id'] ) && 0 < $_POST['form_id'] ) {
			global $wpdb;
			$form_id = intval( $_POST['form_id'] );
			$today = date( "Y-m-d" );
			$upload_dir = wp_upload_dir();
			$dir = $upload_dir['baseurl'];
			$replace_dir = '/uacf7-uploads/';
			$file_name = get_the_title( $form_id );
			$file_name = str_replace( " ", "-", $file_name );
			$form_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "uacf7_form WHERE form_id = %d ", $form_id ) );

			$list = [];
			$count = 0;

			foreach ( $form_data as $fkey => $fdata ) {
				$ContactForm = WPCF7_ContactForm::get_instance( $fdata->form_id );
				$form_fields = $ContactForm->scan_form_tags();
				$fields = [];
				$data = json_decode( $fdata->form_value );
				foreach ( $form_fields as $field ) {

					if ( $field['type'] != 'submit' && $field['type'] != 'uacf7_step_start' && $field['type'] != 'uacf7_step_end' && $field['type'] != 'uarepeater' && $field['type'] == 'uacf7_conversational_start' && $field['type'] == 'uacf7_conversational_end' ) {
						$fields[ $field['name'] ] = '';
					}
				}
				foreach ( $data as $key => $value ) {
					$fields[ $key ] = $value;
				}
				$list_head = [];
				$list_data = [];
				foreach ( $fields as $key => $value ) {
					if ( $fkey == 0 ) {
						$list_head[] = $key;
					}
					if ( is_array( $value ) ) {
						$value = implode( ", ", $value );
					}
					if ( strstr( $value, $replace_dir ) ) {
						$value = str_replace( $replace_dir, "", $value );
						$value = $dir . $replace_dir . $value;
					}
					$list_data[] = $value;
				}
				if ( $fkey == 0 ) {
					$list_head[] = 'Date';
					$list[] = $list_head;
				}
				$list_data[] = $fdata->form_date;
				$list[] = $list_data;


			}
			// Set the headers
			ob_start();
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
			$fp = fopen( 'php://output', 'w' );

			foreach ( $list as $fields ) {
				fputcsv( $fp, $fields );
			}
			fclose( $fp );
			$csv_data = ob_get_clean();
			$data = [ 
				'status' => true,
				'file_name' => $file_name,
				'csv' => $csv_data,
			];
		} else {
			$data = [ 
				'status' => false,
				'message' => esc_html( 'Something went wrong! Form ID not found.', 'ultimate-addons-cf7' ),
			];
		}


		wp_send_json( $data );
		wp_die();

	}

}

/*
 * WP_List_Table Class Call
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/*
 * extends uacf7_form_List_Table class will create the page to load the table
 */

class uacf7_form_List_Table extends WP_List_Table {

	/**
	 * Prepare the items for the table to process
	 *
	 * @return $columns
	 */

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$data = $this->table_data();
		$this->process_bulk_action();
		$perPage = 10;
		$currentPage = $this->get_pagenum();
		$totalItems = count( $data );

		$this->set_pagination_args(
			array(
				'total_items' => $totalItems,
				'per_page' => $perPage
			)
		);

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );

		$this->_column_headers = array( $columns, $hidden );
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */

	public function get_columns() {

		$form_id = empty( $_GET['form_id'] ) ? 0 : (int) $_GET['form_id'];

		$ContactForm = WPCF7_ContactForm::get_instance( $form_id );

		if ( $ContactForm != NULL ) {
			$form_fields = $ContactForm->scan_form_tags();

			$columns = [];
			$columns['cb'] = '<input type="checkbox" />';
			$count = count( $form_fields );
			$count_item = 4;
			$count_i = 0;

			for ( $x = 0; $x < $count; $x++ ) {
				if ( $form_fields[ $x ]['type'] != 'submit' && $form_fields[ $x ]['type'] != 'uacf7_step_start' && $form_fields[ $x ]['type'] != 'uacf7_step_end' && $form_fields[ $x ]['type'] != 'uarepeater' && $form_fields[ $x ]['type'] != 'conditional' && $form_fields[ $x ]['type'] != 'uacf7_conversational_start' && $form_fields[ $x ]['type'] != 'uacf7_conversational_end' && $form_fields[ $x ]['type'] != 'uacf7_signature' && $form_fields[ $x ]['type'] != 'uacf7_signature*' ) {

					if ( $count_i == $count_item ) {
						break;
					}

					$columns[ $form_fields[ $x ]['name'] ] = $form_fields[ $x ]['name'];
					$count_i++;
				}
			}
		}


		// Checked Star Review Status
		if ( $this->uacf7_star_review_status( $form_id ) == true ) {
			$columns['review_publish'] = 'Review Publish';
		}

		$columns['action'] = 'Action';
		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */

	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */

	private function table_data() {
		global $wpdb;
		$form_id = empty( $_GET['form_id'] ) ? 0 : (int) $_GET['form_id'];
		$search = empty( $_REQUEST['s'] ) ? false : esc_sql( $_REQUEST['s'] );
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['baseurl'];
		$replace_dir = '/uacf7-uploads/';
		$data = [];
		if ( isset( $search ) && ! empty( $search ) ) {

			$form_data = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "uacf7_form WHERE form_id = %d AND form_value LIKE '%$search%' ORDER BY id DESC", $form_id )
			);
		} else {
			$form_data = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "uacf7_form WHERE form_id = %d ORDER BY id DESC", $form_id )
			);
		}
		foreach ( $form_data as $fdata ) {
			$f_data = [];
			$field_data = json_decode( $fdata->form_value );
			$repetar_value = '';
			$repetar_key = '';
			$pdf_post_meta = uacf7_get_form_option( $fdata->form_id, 'pdf_generator' );
			$enable_pdf = isset( $pdf_post_meta['uacf7_enable_pdf_generator'] ) ? $pdf_post_meta['uacf7_enable_pdf_generator'] : false;
			$uacf7_enable_pdf_generator_field = uacf7_settings( 'uacf7_enable_pdf_generator_field' );
			if ( $enable_pdf == true && $uacf7_enable_pdf_generator_field = true ) {
				$pdf_btn = "<button data-form-id='" . esc_attr( $fdata->form_id ) . "' data-id='" . esc_attr( $fdata->id ) . "' data-value='" . esc_html( $fdata->form_value ) . "' class='button-primary uacf7-db-pdf'> Export as PDF</button>";
			} else {
				$pdf_btn = '';
			}

			$order_btn = isset( $field_data->order_id ) && $field_data->order_id != 0 ? "<a target='_blank' href='" . admin_url( 'post.php?post=' . $field_data->order_id . '&action=edit' ) . "' class='button-primary uacf7-db-pdf'> View Order</a>" : '';
			foreach ( $field_data as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = implode( ", ", $value );
				} else {
					$value = esc_html( $value );
				}

				if ( strstr( $value, $replace_dir ) ) {
					$value = str_replace( $replace_dir, "", $value );
					$f_data[ $key ] = '<a href="' . $dir . $replace_dir . $value . '" target="_blank">' . esc_html( $value ) . '</a>';
				} else {
					$f_data[ $key ] = esc_html( $value );
				}
				if ( strpos( $key, '__' ) !== false ) {
					$repetar_key = explode( '__', $key );
					$repetar_key = $repetar_key[0];
					$f_data[ $repetar_key ] = esc_html( $value );
				}
			}
			$f_data['id'] = $fdata->id;

			// Checked Star Review Status
			if ( $this->uacf7_star_review_status( $form_id ) == true ) {
				$checked = $fdata->is_review == 1 ? 'checked' : '';
				$f_data['review_publish'] = '<label class="uacf7-admin-toggle1 uacf7_star_label" for="uacf7_review_status_' . esc_attr( $fdata->id ) . '">
                <input type="checkbox" class="uacf7-admin-toggle__input star_is_review" value="' . esc_attr( $fdata->id ) . '"  name="uacf7_review_status_' . esc_attr( $fdata->id ) . '" id="uacf7_review_status_' . esc_attr( $fdata->id ) . '" ' . esc_attr( $checked ) . '>
                <span class="uacf7-admin-toggle-track"><span class="uacf7-admin-toggle-indicator"><span class="checkMark"><svg viewBox="0 0 24 24" id="ghq-svg-check" role="presentation" aria-hidden="true"><path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path></svg></span></span></span>
            </label>';

			}

			$f_data['action'] = "<button data-id='" . esc_attr( $fdata->id ) . "' data-value='" . esc_html( $fdata->form_value ) . "' class='button-primary uacf7-db-view'>" . __( 'View', 'ultimate-addons-cf7' ) . "</button>" . $pdf_btn . $order_btn;
			$data[] = $f_data;
		}
		return $data;
	}

	/**
	 * Define what data to show on each column of the table 
	 */

	public function column_default( $item, $column_name ) {
		// echo "<pre>";
		// print_r($item);
		if ( isset( $item[ $column_name ] ) ) {
			return $item[ $column_name ];
		}
	}


	/**
	 * Single row add css class for unread data
	 * 
	 */

	public function single_row( $item ) {
		$cssClass = ( $item['status'] == 'unread' ) ? 'unread' : 'read';
		echo '<tr class="' . $cssClass . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Culumn checkbox for data filter
	 * 
	 */

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="uacf7_db_id[]" value="%1$s" />',
			$item['id']
		);
	}

	/**
	 * Bulk action
	 * 
	 */

	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'visual-form-builder' ),
		);
		return $actions;
	}


	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();

			/**
			 * Filters the items in the bulk actions menu of the list table.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen.
			 *
			 * @since 3.1.0
			 * @since 5.6.0 A bulk action can now contain an array of options in order to create an optgroup.
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . __( 'Bulk actions' ) . "</option>\n";

		foreach ( $this->_actions as $key => $value ) {
			if ( is_array( $value ) ) {
				echo "\t" . '<optgroup label="' . esc_attr( $key ) . '">' . "\n";

				foreach ( $value as $name => $title ) {
					$class = ( 'edit' === $name ) ? ' class="hide-if-no-js"' : '';

					echo "\t\t" . '<option value="' . esc_attr( $name ) . '"' . $class . '>' . $title . "</option>\n";
				}
				echo "\t" . "</optgroup>\n";
			} else {
				$class = ( 'edit' === $key ) ? ' class="hide-if-no-js"' : '';

				echo "\t" . '<option value="' . esc_attr( $key ) . '"' . $class . '>' . $value . "</option>\n";
			}
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction $two" ) );
		echo "<button  style=' margin-left:5px;' class='button uacf7-db-export-csv'> Export CSV</button>";
		echo "\n";
	}


	/**
	 * Bulk action Filter
	 * 
	 */

	function process_bulk_action() {
		global $wpdb;
		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_POST['uacf7_db_id'] ) ? $_POST['uacf7_db_id'] : array();
			foreach ( $ids as $id ) {
				$id = absint( $id );
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "uacf7_form WHERE id = %d", $id ) );
			}
		}
	}

	// Checked Star Review Status Function
	public function uacf7_star_review_status( $id ) {
		if ( class_exists( 'UACF7_STAR_RATING_PRO' ) && class_exists( 'UACF7_STAR_RATING' ) ) {
			return apply_filters( 'uacf7_star_review_status', false, $id ); // checked star review status
		} else {
			return false;
		}
	}

}
new UACF7_DATABASE();