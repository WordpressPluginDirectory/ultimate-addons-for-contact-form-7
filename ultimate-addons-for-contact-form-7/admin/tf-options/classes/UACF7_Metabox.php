<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UACF7_Metabox' ) ) {
	class UACF7_Metabox {

		public $metabox_id = null;
		public $metabox_title = null;
		public $metabox_post_type = null;
		public $metabox_sections = array();

		public function __construct( $key, $params = array() ) {

			$this->metabox_id = $key;
			$this->metabox_title = $params['title'];
			$this->metabox_post_type = $params['post_type'];
			$this->metabox_sections = $params['sections'];


			add_action( 'add_meta_boxes', array( $this, 'tf_meta_box' ) );

			if ( $this->metabox_post_type == 'uacf7' ) {

				add_action( 'wpcf7_admin_footer', array( $this, 'tf_meta_box_content' ), 20, 2 );
			}

			add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
			// add_action( 'wpcf7_after_save', array( $this, 'save_metabox' ), 10, 2 );

			//load fields
			$this->load_fields();
		}

		public static function metabox( $key, $params = array() ) {
			return new self( $key, $params );
		}

		/*
		 * Load fields
		 * @author Foysal
		 */
		public function load_fields() {

			// Fields Class
			require_once UACF7_PATH . 'admin/tf-options/fields/UACF7_Fields.php';

			$fields = glob( UACF7_PATH . 'admin/tf-options/fields/*/UACF7_*.php' );

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$field_name = basename( $field, '.php' );
					if ( ! class_exists( $field_name ) ) {
						require_once $field;
					}
				}
			}

		}

		/**
		 * Metaboxes
		 * @author Foysal
		 */
		public function tf_meta_box() {
			if ( get_post_type() !== $this->metabox_post_type ) {
				return;
			}
			add_meta_box( $this->metabox_id, "<a href='#' class='tf-mobile-tabs'><i class='fa-solid fa-bars'></i></a>" . $this->metabox_title, array(
				$this,
				'tf_meta_box_content'
			), $this->metabox_post_type, 'normal', 'high', );
		}

		/*
		 * Metabox Content
		 * @author Sydur
		 */
		public function tf_meta_box_content( $post ) {

			if ( empty( $this->metabox_sections ) ) {
				return;
			}

			$display = $this->metabox_post_type == 'uacf7' ? 'display:none;' : 'display:block;'
				?>

			<div id="uacf7-metabox" class="uacf7-metabox <?php echo esc_attr( $this->metabox_post_type ); ?>"
				style="<?php echo esc_attr( $display ); ?>">
				<div class="tf-setting-dashboard">

					<div class="tf-option-wrapper tf-setting-wrapper">
						<?php
						// Add nonce for security and authentication.
						wp_nonce_field( 'tf_meta_box_nonce_action', 'tf_meta_box_nonce' );

						// Retrieve an existing value from the database.
						if ( $post->ID != null ) {
							$tf_meta_box_value = get_post_meta( $post->ID, $this->metabox_id, true );
						} else {
							$tf_meta_box_value = get_post_meta( $post->id(), $this->metabox_id, true );
						}

						// Set default values.
						if ( empty( $tf_meta_box_value ) ) {
							$tf_meta_box_value = array();
						}
						?>
						<?php if ( $this->metabox_post_type == 'uacf7' ) : ?>
							<div class="tf-metabox-title">
								<h3>
									<?php echo esc_html( 'Ultimate Addons for CF7 Options', 'ultimate-addons-cf7' ) ?>
								</h3>

								<span>
									<?php echo esc_html( 'VERSION : ', 'ultimate-addons-cf7' ); ?>
									<?php echo UACF7_VERSION ?>
								</span>
							</div>
						<?php endif; ?>
						<div class="tf-admin-meta-box tf-option-form">
							<div class="tf-option">
								<div class="tf-admin-tab ">
									<?php
									$section_count = 0;
									$sections = $this->metabox_sections;

									asort( $sections );


									if ( isset( $sections['import_export'] ) ) {
										$import_export = $sections['import_export'];
										unset( $sections['import_export'] );
										$sections['import_export'] = $import_export;

									}


									foreach ( $sections as $key => $section ) : ?>
										<?php //if( $section != null): ?>
										<a class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
											data-tab="<?php echo esc_attr( $key ) ?>">

											<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
											<?php echo esc_html( $section['title'] ); ?>
											<?php if ( isset( $section['checked_field'] ) && $section['checked_field'] ) {
												$checked = isset( $tf_meta_box_value[ $key ][ $section['checked_field'] ] ) ? $tf_meta_box_value[ $key ][ $section['checked_field'] ] : '';
												if ( $checked != '' || $checked == true ) {
													echo ' <span class="tf-metabox-option-checked"></span>';
												}
											}
											?>
										</a>
										<?php // endif; ?>
										<?php $section_count++; endforeach; ?>
								</div>

								<div class="tf-tab-wrapper">
									<?php $content_count = 0;
									foreach ( $sections as $key => $section ) : ?>
										<div id="<?php echo esc_attr( $key ) ?>"
											class="tf-tab-content tf-container <?php echo $content_count == 0 ? 'active' : ''; ?>">

											<?php
											if ( ! empty( $section['fields'] ) ) :
												foreach ( $section['fields'] as $field ) :

													$default = isset( $field['default'] ) ? $field['default'] : '';
													$value = isset( $tf_meta_box_value[ $key ][ $field['id'] ] ) ? $tf_meta_box_value[ $key ][ $field['id'] ] : $default;

													$tf_option = new UACF7_Options();
													// $tf_option->field( $field, $value, $this->option_id );
													$tf_option->field( $field, $value, $this->metabox_id, '', $key );
												endforeach;
											endif; ?>

										</div>
										<?php $content_count++; endforeach; ?>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/*
		 * Save Metabox
		 * @author Foysal
		 */
		public function save_metabox( $post_id ) {
			// Add nonce for security and authentication.
			$nonce_name = isset( $_POST['tf_meta_box_nonce'] ) ? $_POST['tf_meta_box_nonce'] : '';
			$nonce_action = 'tf_meta_box_nonce_action';

			// $post_id = $form->id();

			// Check if a nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			// Check if the user has permissions to save data.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check if it's not an autosave.
			if ( wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Check if it's not a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}
			$meta_data = get_post_meta( $post_id, $this->metabox_id, true );
			if ( $meta_data ) {
				$tf_meta_box_value = $meta_data;
			} else {
				$tf_meta_box_value = array();
			}
			$metabox_request = ( ! empty( $_POST[ $this->metabox_id ] ) ) ? $_POST[ $this->metabox_id ] : array();

			if ( ! empty( $metabox_request ) && ! empty( $this->metabox_sections ) ) {
				// uacf7_print_r($metabox_request);
				// exit;
				foreach ( $this->metabox_sections as $section_key => $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {
								$data = isset( $metabox_request[ $section_key ][ $field['id'] ] ) ? $metabox_request[ $section_key ][ $field['id'] ] : '';

								$fieldClass = 'UACF7_' . $field['type'];
								$data = $fieldClass == 'UACF7_map' ? serialize( $data ) : $data;

								if ( class_exists( $fieldClass ) ) {
									$_field = new $fieldClass( $field, $data, $this->metabox_id );
									$tf_meta_box_value[ $section_key ][ $field['id'] ] = $_field->sanitize();
								}

							}
						}
					}
				}
			}

			if ( ! empty( $tf_meta_box_value ) ) {
				//            
				$meta_data = apply_filters( 'tf_metabox_before_save_option', $tf_meta_box_value, $post_id );

				update_post_meta( $post_id, $this->metabox_id, $meta_data );
			} else {
				delete_post_meta( $post_id, $this->metabox_id );
			}

		}

	}
}


