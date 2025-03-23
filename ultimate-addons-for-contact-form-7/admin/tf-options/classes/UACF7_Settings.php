<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UACF7_Settings' ) ) {
	class UACF7_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_icon = null;
		public $option_position = null;
		public $option_sections = array();
		public $pre_tabs = '';
		public $pre_fields = '';
		public $pre_sections = '';

		public function __construct( $key, $params = array() ) {
			$this->option_id = $key;
			$this->option_title = ! empty( $params['title'] ) ? apply_filters( $key . '_title', $params['title'] ) : '';
			$this->option_icon = ! empty( $params['icon'] ) ? apply_filters( $key . '_icon', $params['icon'] ) : '';
			$this->option_position = ! empty( $params['position'] ) ? apply_filters( $key . '_position', $params['position'] ) : 30.01;
			$this->option_sections = ! empty( $params['sections'] ) ? apply_filters( $key . '_sections', $params['sections'] ) : array();
			// echo $this->option_icon;
			// run only is admin panel options, avoid performance loss
			$this->pre_tabs = $this->pre_tabs( $this->option_sections );
			$this->pre_fields = $this->pre_fields( $this->option_sections );
			$this->pre_sections = $this->pre_sections( $this->option_sections );

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ), 10, 2 );

			//save options
			add_action( 'admin_init', array( $this, 'save_options' ) );

			//ajax save options
			add_action( 'wp_ajax_uacf7_options_save', array( $this, 'uacf7_ajax_save_options' ) );
			
			add_action('wp_ajax_themefic_manage_plugin', array( $this, 'themefic_manage_plugin' ) );
		}

		public static function option( $key, $params = array() ) {
			return new self( $key, $params );
		}

		public function pre_tabs( $sections ) {

			$result = array();
			$parents = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['parent'] ) ) {
					$parents[ $section['parent'] ][ $key ] = $section;
					unset( $sections[ $key ] );
				}
			}

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $key ) && ! empty( $parents[ $key ] ) ) {
					$section['sub_section'] = $parents[ $key ];
				}
				$result[ $key ] = $section;
			}

			return $result;
		}

		public function pre_fields( $sections ) {

			$result = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$result[] = $field;
					}
				}
			}

			return $result;
		}

		public function pre_sections( $sections ) {

			$result = array();

			foreach ( $this->pre_tabs as $tab ) {
				if ( ! empty( $tab['subs'] ) ) {
					foreach ( $tab['subs'] as $sub ) {
						$sub['ptitle'] = $tab['title'];
						$result[] = $sub;
					}
				}
				if ( empty( $tab['subs'] ) ) {
					$result[] = $tab;
				}
			}

			return $result;
		}

		/**
		 * Options Page menu
		 * @author Foysal
		 */
		public function tf_options() {

			add_menu_page(
				$this->option_title,
				$this->option_title,
				'manage_options',
				$this->option_id,
				array( $this, 'tf_options_page' ),
				$this->option_icon,
				$this->option_position
			);
			//Addons submenu
			add_submenu_page(
				$this->option_id,
				__( 'All Addons', 'ultimate-addons-cf7' ),
				__( 'All Addons', 'ultimate-addons-cf7' ),
				'manage_options',
				'uacf7_addons',
				array( $this, 'uacf7_addons_page' ),
			);

			// All Forms
			add_submenu_page(
				$this->option_id,
				__( 'All Forms', 'ultimate-addons-cf7' ),
				__( 'All Forms', 'ultimate-addons-cf7' ),
				'manage_options',
				'admin.php?page=wpcf7',
				// array( $this, 'uacf7_create_database_page' ),
			);
			// All Forms
			add_submenu_page(
				$this->option_id,
				__( 'Add New Form', 'ultimate-addons-cf7' ),
				__( 'Add New Form', 'ultimate-addons-cf7' ),
				'manage_options',
				'admin.php?page=wpcf7-new',
				// array( $this, 'uacf7_create_database_page' ),
			);


			// 
			add_submenu_page(
				$this->option_id, //parent slug
				__( 'Settings', 'ultimate-addons-cf7' ), // page_title
				__( 'Settings', 'ultimate-addons-cf7' ), // menu_title
				'manage_options', // capability
				$this->option_id . '#tab=mailchimp', // menu_slug
				array( $this, 'tf_options_page' ) // function
			);

			if ( class_exists( 'Ultimate_Addons_CF7_PRO' ) ) {
				//License Info submenu 
				add_submenu_page(
					$this->option_id, //parent slug
					__( 'Pro License', 'ultimate-addons-cf7' ),
					__( 'Pro License', 'ultimate-addons-cf7' ),
					'manage_options',
					'uacf7_license_info',
					array( $this, 'uacf7_license_info_callback' ),
				);
			}

			// //Get Help submenu
			// add_submenu_page(
			// 	$this->option_id, //parent slug
			// 	__('Get Help', 'ultimate-addons-cf7'),
			// 	__('Get Help', 'ultimate-addons-cf7'),
			// 	'manage_options',
			// 	'tf_get_help',
			// 	array( $this,'tf_get_help_callback'),
			// 	10,
			// );


			// remove first submenu
			remove_submenu_page( $this->option_id, $this->option_id );

		}

		// page top header
		function tf_top_header() {
			?>
			<div class="tf-setting-top-bar">
				<div class="version">
					<img style="height:60px; width:60px;" src="<?php echo UACF7_URL; ?>assets/img/uacf7-icon.png" alt="logo">
					<span>v<?php echo esc_attr( UACF7_VERSION ); ?></span>
				</div>
				<div class="other-document">
					<svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg"
						style="color: #003c79;background: ;">
						<path
							d="M19.2106 0H6.57897C2.7895 0 0.263184 2.52632 0.263184 6.31579V13.8947C0.263184 17.6842 2.7895 20.2105 6.57897 20.2105V22.9011C6.57897 23.9116 7.70318 24.5179 8.53687 23.9495L14.1579 20.2105H19.2106C23 20.2105 25.5263 17.6842 25.5263 13.8947V6.31579C25.5263 2.52632 23 0 19.2106 0ZM12.8948 15.3726C12.3642 15.3726 11.9474 14.9432 11.9474 14.4253C11.9474 13.9074 12.3642 13.4779 12.8948 13.4779C13.4253 13.4779 13.8421 13.9074 13.8421 14.4253C13.8421 14.9432 13.4253 15.3726 12.8948 15.3726ZM14.4863 10.1305C13.9937 10.4589 13.8421 10.6737 13.8421 11.0274V11.2926C13.8421 11.8105 13.4127 12.24 12.8948 12.24C12.3769 12.24 11.9474 11.8105 11.9474 11.2926V11.0274C11.9474 9.56211 13.0211 8.84211 13.4253 8.56421C13.8927 8.24842 14.0442 8.03368 14.0442 7.70526C14.0442 7.07368 13.5263 6.55579 12.8948 6.55579C12.2632 6.55579 11.7453 7.07368 11.7453 7.70526C11.7453 8.22316 11.3158 8.65263 10.7979 8.65263C10.28 8.65263 9.85055 8.22316 9.85055 7.70526C9.85055 6.02526 11.2148 4.66105 12.8948 4.66105C14.5748 4.66105 15.939 6.02526 15.939 7.70526C15.939 9.14526 14.8779 9.86526 14.4863 10.1305Z"
							fill="#003c79"></path>
					</svg>

					<div class="dropdown">
						<div class="list-item">
							<a href="https://portal.themefic.com/support/" target="_blank">
								<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M10.0482 4.37109H4.30125C4.06778 4.37109 3.84329 4.38008 3.62778 4.40704C1.21225 4.6137 0 6.04238 0 8.6751V12.2693C0 15.8634 1.43674 16.5733 4.30125 16.5733H4.66044C4.85799 16.5733 5.1184 16.708 5.23514 16.8608L6.3127 18.2985C6.78862 18.9364 7.56087 18.9364 8.03679 18.2985L9.11435 16.8608C9.24904 16.6811 9.46456 16.5733 9.68905 16.5733H10.0482C12.6793 16.5733 14.107 15.3692 14.3136 12.9432C14.3405 12.7275 14.3495 12.5029 14.3495 12.2693V8.6751C14.3495 5.80876 12.9127 4.37109 10.0482 4.37109ZM4.04084 11.5594C3.53798 11.5594 3.14288 11.1551 3.14288 10.6609C3.14288 10.1667 3.54696 9.76233 4.04084 9.76233C4.53473 9.76233 4.93881 10.1667 4.93881 10.6609C4.93881 11.1551 4.53473 11.5594 4.04084 11.5594ZM7.17474 11.5594C6.67188 11.5594 6.27678 11.1551 6.27678 10.6609C6.27678 10.1667 6.68086 9.76233 7.17474 9.76233C7.66862 9.76233 8.07271 10.1667 8.07271 10.6609C8.07271 11.1551 7.6776 11.5594 7.17474 11.5594ZM10.3176 11.5594C9.81476 11.5594 9.41966 11.1551 9.41966 10.6609C9.41966 10.1667 9.82374 9.76233 10.3176 9.76233C10.8115 9.76233 11.2156 10.1667 11.2156 10.6609C11.2156 11.1551 10.8115 11.5594 10.3176 11.5594Z"
										fill="#003c79"></path>
									<path
										d="M17.9423 5.08086V8.67502C17.9423 10.4721 17.3855 11.6941 16.272 12.368C16.0026 12.5298 15.6884 12.3141 15.6884 11.9996L15.6973 8.67502C15.6973 5.08086 13.641 3.0232 10.0491 3.0232L4.58048 3.03219C4.26619 3.03219 4.05067 2.7177 4.21231 2.44814C4.88578 1.33395 6.10702 0.776855 7.89398 0.776855H13.641C16.5055 0.776855 17.9423 2.21452 17.9423 5.08086Z"
										fill="#003c79"></path>
								</svg>
								<span><?php _e( "Need Help?", "ultimate-addons-cf7" ); ?></span>
							</a>
							<a href="https://themefic.com/docs/uacf7/" target="_blank">
								<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M16.1896 7.57803H13.5902C11.4586 7.57803 9.72274 5.84103 9.72274 3.70803V1.10703C9.72274 0.612031 9.318 0.207031 8.82332 0.207031H5.00977C2.23956 0.207031 0 2.00703 0 5.22003V13.194C0 16.407 2.23956 18.207 5.00977 18.207H12.0792C14.8494 18.207 17.089 16.407 17.089 13.194V8.47803C17.089 7.98303 16.6843 7.57803 16.1896 7.57803ZM8.09478 14.382H4.4971C4.12834 14.382 3.82254 14.076 3.82254 13.707C3.82254 13.338 4.12834 13.032 4.4971 13.032H8.09478C8.46355 13.032 8.76935 13.338 8.76935 13.707C8.76935 14.076 8.46355 14.382 8.09478 14.382ZM9.89363 10.782H4.4971C4.12834 10.782 3.82254 10.476 3.82254 10.107C3.82254 9.73803 4.12834 9.43203 4.4971 9.43203H9.89363C10.2624 9.43203 10.5682 9.73803 10.5682 10.107C10.5682 10.476 10.2624 10.782 9.89363 10.782Z"
										fill="#003c79"></path>
								</svg>
								<span><?php _e( "Documentation", "ultimate-addons-cf7" ); ?></span>

							</a>
							<a href="https://portal.themefic.com/support/" target="_blank">
								<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd"
										d="M13.5902 7.57803H16.1896C16.6843 7.57803 17.089 7.98303 17.089 8.47803V13.194C17.089 16.407 14.8494 18.207 12.0792 18.207H5.00977C2.23956 18.207 0 16.407 0 13.194V5.22003C0 2.00703 2.23956 0.207031 5.00977 0.207031H8.82332C9.318 0.207031 9.72274 0.612031 9.72274 1.10703V3.70803C9.72274 5.84103 11.4586 7.57803 13.5902 7.57803ZM11.9613 0.396012C11.5926 0.0270125 10.954 0.279013 10.954 0.792013V3.93301C10.954 5.24701 12.0693 6.33601 13.4274 6.33601C14.2818 6.34501 15.4689 6.34501 16.4852 6.34501H16.4854C16.998 6.34501 17.2679 5.74201 16.9081 5.38201C16.4894 4.96018 15.9637 4.42927 15.3988 3.85888L15.3932 3.85325L15.3913 3.85133L15.3905 3.8505L15.3902 3.85016C14.2096 2.65803 12.86 1.29526 11.9613 0.396012ZM3.0145 12.0732C3.0145 11.7456 3.28007 11.48 3.60768 11.48H5.32132V9.76639C5.32132 9.43879 5.58689 9.17321 5.9145 9.17321C6.2421 9.17321 6.50768 9.43879 6.50768 9.76639V11.48H8.22131C8.54892 11.48 8.8145 11.7456 8.8145 12.0732C8.8145 12.4008 8.54892 12.6664 8.22131 12.6664H6.50768V14.38C6.50768 14.7076 6.2421 14.9732 5.9145 14.9732C5.58689 14.9732 5.32132 14.7076 5.32132 14.38V12.6664H3.60768C3.28007 12.6664 3.0145 12.4008 3.0145 12.0732Z"
										fill="#003c79"></path>
								</svg>
								<span><?php _e( "Feature Request", "ultimate-addons-cf7" ); ?></span>
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}


		/**
		 * Get UAC7 Addon Page
		 * @author Sydur Rahman
		 */

		public function uacf7_addons_page() {
			// uacf7_print_r($this->option_sections);

			?>
			<div class="tf-setting-dashboard">
				<!-- deshboard-header-include -->
				<?php echo $this->tf_top_header(); ?>
				<div class="uacf7-addons-settings-page">
					<div class="uacf7-addons-settings-page-wrapper">
						<h1 class="uacf7-setting-title">
							<?php echo _e( 'Ultimate Addons for Contact Form 7 (UACF7) Settings', 'ultimate-addons-cf7' ) ?>
						</h1>
						<form method="post" action="" class="tf-option-form tf-ajax-save" enctype="multipart/form-data">
							<div class="uacf7-settings-heading">
								<div class="uacf7-settings-heading-wrap">
									<label for="uacf7-addon-filter" class="uacf7-addon-filter-search">
										<span class="uacf7-addon-filter-icon">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none"
												xmlns="http://www.w3.org/2000/svg">
												<path
													d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z"
													stroke="#D5D0E2" stroke-width="1.66667" stroke-linecap="round"
													stroke-linejoin="round" />
											</svg>
										</span>
										<input id="uacf7-addon-filter" type="text" name="uacf7_addon_filter">
									</label>
								</div>
								<div class="uacf7-settings-heading-wrap">
									<div class="uacf7-addon-filter-cta">
										<button
											class="uacf7-addon-filter-button all active"><?php echo _e( 'All', 'ultimate-addons-cf7' ) ?>
											( <span class="uacf7-addon-filter-cta-count"></span> )</button>
										<button
											class="uacf7-addon-filter-button deactive"><?php echo _e( 'Free', 'ultimate-addons-cf7' ) ?>
											( <span class="uacf7-addon-filter-cta-count"></span> )</button>
										<button
											class="uacf7-addon-filter-button activete"><?php echo _e( 'Pro', 'ultimate-addons-cf7' ) ?>
											( <span class="uacf7-addon-filter-cta-count"></span> )</button>
									</div>
								</div>
							</div>

							<div class="uacf7-addon-setting-content">
								<input type="hidden" name="uacf7_current_page" value="uacf7_addons_page">
								<?php
								$data = get_option( $this->option_id, true );

								$fields = [];

								foreach ( $this->option_sections as $section_key => $section ) :

									if ( $section_key == 'general_addons' || $section_key == 'extra_fields_addons' || $section_key == 'wooCommerce_integration' ) :
										$fields = array_merge( $fields, $section['fields'] );
									endif;
								endforeach;

								//  Short as Alphabetically
								usort( $fields, array( $this, 'uacf7_setup_wizard_sorting' ) );
								foreach ( $fields as $field_key => $field ) :
									$id = $this->option_id . '[' . $field['id'] . ']';
									?>
									<div class="uacf7-single-addon-setting uacf7-fields-<?php echo esc_attr( $field['id'] ) ?>"
										data-parent="<?php echo esc_attr( $section_key ) ?>"
										data-filter="<?php echo esc_html( strtolower( $field['label'] ) ) ?>">
										<?php
										$label_class = '';
										if ( isset( $field['is_pro'] ) ) {
											$label_class .= $field['is_pro'] == true ? 'tf-field-disable tf-field-pro' : '';
											echo '<span class="addon-status pro">' . esc_html( 'Pro' ) . '</span>';
										} else {
											echo '<span class="addon-status free">' . esc_html( 'Free' ) . '</span>';
										}
										$child = isset( $field['child_field'] ) ? $field['child_field'] : '';
										$is_pro = isset( $field['is_pro'] ) ? 'pro' : '';
										$default = $field['default'] == true ? 'checked' : '';
										$default = isset( $data[ $field['id'] ] ) && $data[ $field['id'] ] == 1 ? 'checked' : $default;
										$value = isset( $data[ $field['id'] ] ) ? $data[ $field['id'] ] : 0;
										$demo_link = isset( $field['demo_link'] ) ? $field['demo_link'] : '#';
										$documentation_link = isset( $field['documentation_link'] ) ? $field['documentation_link'] : '#';

										// echo $default; 
										?>
										<div class="uacf7-single-addons-wrap">
											<?php if ( isset( $field['image_url'] ) && ! empty( $field['image_url'] ) ) : ?>
												<img src="<?php echo esc_url( $field['image_url'] ); ?>" alt="">
											<?php endif; ?>
											<h2 class="uacf7-single-addon-title"><?php echo esc_html( $field['label'] ) ?></h2>
											<p class="uacf7-single-addon-desc">
												<?php echo isset( $field['subtitle'] ) ? $field['subtitle'] : ''; ?>
												<?php echo '<a href="' . sanitize_url( $documentation_link ) . '" target="_blank">' . __( 'Documentation', 'ultimate-addons-cf7' ) . '</a>' ?>
											</p>

										</div>
										<div class="uacf7-single-addon-cta">
											<a href="<?php echo sanitize_url( $demo_link ); ?>" target="_blank"
												class="uacf7-single-addon-btn">View Demo</a>

											<div class="uacf7-addon-toggle-wrap">
												<input type="checkbox" data-child="<?php echo esc_attr( $child ) ?>"
													data-is-pro="<?php echo esc_attr( $is_pro ) ?>"
													id="<?php echo esc_attr( $field['id'] ) ?>" <?php echo esc_attr( $default ) ?>
													value="<?php echo esc_html( $value ); ?>" class="uacf7-addon-input-field"
													name="<?php echo esc_attr( $id ) ?>" id="uacf7_enable_redirection">

												<label class="uacf7-addon-toggle-inner <?php echo esc_attr( $label_class ) ?> "
													for="<?php echo esc_attr( $field['id'] ) ?>">
													<span class="uacf7-addon-toggle-track"><svg width="16" height="17" viewBox="0 0 16 17"
															fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect y="0.5" width="16" height="16" rx="8" fill="#79757F" />
														</svg>
													</span>
												</label>
											</div>

										</div>
									</div>

									<?php
								endforeach;
								?>
							</div>
							<?php wp_nonce_field( 'uacf7_option_nonce_action', 'uacf7_option_nonce' ); ?>
						</form>
					</div>
					
					<div class="uacf7-addons-settings-sidebar">
						<?php echo $this->tf_sidebar(); ?>
					</div>
				</div>
			</div>

			<?php
		}

		public function tf_sidebar() {
			?>

			<div class="uacf7-sidebar">
				<div class="uacf7-sidebar-wrap">
					<div class="uacf7-sidebar-header">
						<h1 class="uacf7-sidebar-title"><?php echo _e( 'Extend Your Website', 'ultimate-addons-cf7' ) ?></h1>
					</div>
					<div class="uacf7-sidebar-content">
						
						<?php echo $this->tf_get_sidebar_plugin_list(); ?>

						<div class="quick-access">
							<h3>Quick Access</h3>
							<div class="quick-access-wrapper">
								<div class="access-item">
									<a href="https://themefic.com/docs/uacf7/" target="_blank">
										<span class="icon"><i class="fa-solid fa-folder-open"></i></span>
										<?php echo _e( 'Documentation', 'ultimate-addons-cf7' ); ?>
									</a>
								</div>
								<div class="access-item">
									<a href="https://portal.themefic.com/support/" target="_blank">
										<span class="icon"><i class="fa-solid fa-headset"></i></span>
										<?php echo _e( 'Get Support', 'ultimate-addons-cf7' ); ?>
									</a>
								</div>
								<div class="access-item">
									<a href="https://www.facebook.com/groups/ultimate.cf7" target="_blank">
										<span class="icon"><i class="fa-solid fa-users"></i></span>
										<?php echo _e( 'Join our Community', 'ultimate-addons-cf7' ); ?>
									</a>
								</div>
								<div class="access-item">
									<a href="https://app.loopedin.io/ultimate-addons-for-contact-form-7" target="_blank">
										<span class="icon"><i class="fa-solid fa-road-circle-check"></i></span>
										<?php echo _e( 'See our Roadmap', 'ultimate-addons-cf7' ); ?>
									</a>
								</div>
								<div class="access-item">
									<a href="https://app.loopedin.io/ultimate-addons-for-contact-form-7#/ideas" target="_blank">
										<span class="icon"><i class="fa-solid fa-lightbulb"></i></span>
										<?php echo _e( 'Request a Feature', 'ultimate-addons-cf7' ); ?>
									</a>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
			<?php
		}

		public function tf_get_sidebar_plugin_list(){

			$plugins = [
				// [
				// 	'name'       => 'UACF7',
				// 	'slug'       => 'ultimate-addons-for-contact-form-7',
				// 	'file_name'  => 'ultimate-addons-for-contact-form-7',
				// 	'subtitle'   => '40+ Essential Addons for Contact Form 7',
				// 	'image'      => 'https://ps.w.org/ultimate-addons-for-contact-form-7/assets/icon-128x128.png',
				// 	// 'pro'        => [
				// 	// 	'slug'      => 'ultimate-addons-for-contact-form-7-pro',
				// 	// 	'file_name' => 'ultimate-addons-for-contact-form-7-pro',
				// 	// 	'url'       => 'https://cf7addons.com/pricing/',
				// 	// ],
				// ],
				[
					'name'       => 'Hydra',
					'slug'       => 'hydra-booking',
					'file_name'  => 'hydra-booking',
					'subtitle'   => 'All in One Appointment Booking System',
					'image'      => 'https://ps.w.org/hydra-booking/assets/icon-128x128.jpg',
					// 'pro'        => [
					// 	'slug'      => 'hydra-booking-pro',
					// 	'file_name' => 'hydra-booking-pro',
					// 	'url'       => 'https://hydrabooking.com/',
					// ],
				],
				[
					'name'       => 'BEAF',
					'slug'       => 'beaf-before-and-after-gallery',
					'file_name'  => 'before-and-after-gallery',
					'subtitle'   => 'Ultimate Before After Image Slider & Gallery',
					'image'      => 'https://ps.w.org/beaf-before-and-after-gallery/assets/icon-128x128.png',
					// 'pro'        => [
					// 	'slug'      => 'beaf-before-and-after-gallery-pro',
					// 	'file_name' => 'before-and-after-gallery-pro',
					// 	'url'       => 'https://themefic.com/plugins/beaf/pro/',
					// ],
				],
				[
					'name'       => 'Tourfic',
					'slug'       => 'tourfic',
					'file_name'  => 'tourfic',
					'subtitle'   => 'Travel, Hotel Booking & Car Rental WP Plugin',
					'image'      => 'https://ps.w.org/tourfic/assets/icon-128x128.gif',
					// 'pro'        => [
					// 	'slug'      => 'tourfic-pro',
					// 	'file_name' => 'tourfic-pro',
					// 	'url'       => 'https://themefic.com/tourfic/',
					// ],
				],
				[
					'name'       => 'Instantio',
					'slug'       => 'instantio',
					'file_name'  => 'instantio',
					'subtitle'   => 'WooCommerce Quick & Direct Checkout',
					'image'      => 'https://ps.w.org/instantio/assets/icon-128x128.png',
					// 'pro'        => [
					// 	'slug'      => 'wooinstant',
					// 	'file_name' => 'wooinstant',
					// 	'url'       => 'https://themefic.com/instantio/',
					// ],
				],
				// [
				// 	'name'       => 'Before After Slider for WooCommerce – eBEAF',
				// 	'slug'       => 'before-after-for-woocommerce',
				// 	'file_name'  => 'before-after-for-woocommerce',
				// 	'image'      => 'https://ps.w.org/before-after-for-woocommerce/assets/icon-128x128.gif',
				// 	'pro_url'    => '',
				// 	'pro'        => [
				// 		'slug'      => 'before-after-for-woocommerce-pro',
				// 		'file_name' => 'before-after-for-woocommerce-pro',
				// 		'url'       => 'https://themefic.com/plugins/ebeaf/pro/',
				// 	],
				// ],
			];

			?>

			<ul>
				<?php foreach ($plugins as $plugin): 
					$plugin_path = $plugin['slug'] . '/' . $plugin['file_name'] . '.php';
					$installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin_path);
					$activated = $installed && is_plugin_active($plugin_path);

					$pro_installed = false;
					$pro_activated = false;
					
					if (!empty($plugin['pro'])) {
						$pro_path = $plugin['pro']['slug'] . '/' . $plugin['pro']['file_name'] . '.php';
						$pro_installed = file_exists(WP_PLUGIN_DIR . '/' . $pro_path);
						$pro_activated = $pro_installed && is_plugin_active($pro_path);
					}

					?>

					<li class="plugin-item <?php echo esc_attr($plugin['slug'] == 'hydra-booking' ? 'featured' : ''); ?>" data-plugin-slug="<?php echo esc_attr($plugin['slug']); ?>">
						<div class="plugin-info-wrapper">
							<div class="plugin-info">
								<img src="<?php echo esc_url($plugin['image']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>" class="<?php echo esc_attr($plugin['name'] == 'BEAF' ? 'beaf-logo' : ''); ?>" width="40" height="40">
								<div class="plugin-btn">
									<span class="badge free">Free</span>
									<?php if (!$installed): ?>
										<button class="plugin-button install" data-action="install" data-plugin="<?php echo esc_attr($plugin['slug']); ?>" data-plugin_filename="<?php echo esc_attr($plugin['file_name']); ?>">
											Install <span class="loader"></span>
										</button>
									<?php elseif (!$activated): ?>
										<button class="plugin-button activate" data-action="activate" data-plugin="<?php echo esc_attr($plugin['slug']); ?>" data-plugin_filename="<?php echo esc_attr($plugin['file_name']); ?>" >
											Activate <span class="loader"></span>
										</button>
									<?php else: ?>
										<span class="plugin-button plugin-status active">Activated</span>
									<?php endif; ?>

									<?php if (!empty($plugin['pro'])): ?>
										<?php if (!$pro_installed): ?>
											<a href="<?php echo esc_url($plugin['pro']['url']); ?>" class="plugin-button pro" target="_blank">Get Pro</a>
										<?php elseif (!$pro_activated): ?>
											<button class="plugin-button activate-pro" data-action="activate" data-plugin="<?php echo esc_attr($plugin['pro']['slug']); ?>" data-plugin_filename="<?php echo esc_attr($plugin['pro']['file_name']); ?>">
												Activate Pro <span class="loader"></span>
											</button>
										<?php else: ?>
											<span class="plugin-button plugin-status active-pro">Pro Activated</span>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="uacf7-plugin-content">
								<h4><?php echo esc_html($plugin['name']); ?></h4>
								<p><?php echo esc_html($plugin['subtitle']); ?></p>
								<strong></strong>
							</div>
						</div>
					</li>

				<?php endforeach; ?>

			</ul>

			<?php 
		}

		public function themefic_manage_plugin() {
			check_ajax_referer('themefic_plugin_nonce', 'security');

			if (!current_user_can('install_plugins')) {
				wp_send_json_error('You do not have permission to perform this action.');
			}

			$plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field($_POST['plugin_slug']) : '';
			$plugin_filename = isset($_POST['plugin_filename']) ? sanitize_text_field($_POST['plugin_filename']) : '';
			$plugin_action = isset($_POST['plugin_action']) ? sanitize_text_field($_POST['plugin_action']) : '';

			if (!$plugin_slug || !$plugin_action) {
				wp_send_json_error('Invalid request.');
			}

			include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ($plugin_action === 'install') {
				$api = plugins_api('plugin_information', ['slug' => $plugin_slug]);

				if (is_wp_error($api)) {
					wp_send_json_error($api->get_error_message());
				}

				$upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
				$install_result = $upgrader->install($api->download_link);

				if (is_wp_error($install_result)) {
					wp_send_json_error($install_result->get_error_message());
				}

				wp_send_json_success(['message' => 'Installed successfully.']);
			}

			if ($plugin_action === 'activate') {
				$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_filename . '.php';

				if (!file_exists($plugin_path)) {
					wp_send_json_error('Plugin file not found.');
				}

				$activate_result = activate_plugin($plugin_path);

				if (is_wp_error($activate_result)) {
					wp_send_json_error($activate_result->get_error_message());
				}

				wp_send_json_success(['message' => 'Activated successfully.']);
			}

			wp_send_json_error('Invalid action.');
		}



		// Custom comparison function based on 'label' value
		public function uacf7_setup_wizard_sorting( $a, $b ) {
			$labelA = $a['label'][0];
			$labelB = $b['label'][0];
			return strcmp( $labelA, $labelB );
		}

		/**
		 * Get Help Page
		 * @author Jahid
		 */
		public function tf_get_help_callback() {
			?>
			<div class="tf-setting-dashboard">

				<!-- deshboard-header-include -->
				<?php echo $this->tf_top_header(); ?>

				<div class="tf-settings-help-center">
					<div class="tf-help-center-banner">
						<div class="tf-help-center-content">
							<h2><?php _e( "Setup Wizard", "ultimate-addons-cf7" ); ?></h2>
							<p><?php _e( "Click the button below to run the setup wizard of Ultimate Addons for Contact Form 7. Your existing settings will not change.", "ultimate-addons-cf7" ); ?>
							</p>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard' ) ) ?>"
								class="tf-admin-btn tf-btn-secondary"><?php _e( "Setup Wizard", "ultimate-addons-cf7" ); ?></a>
						</div>
						<div class="tf-help-center-image">
							<img src="<?php // echo TF_ASSETS_APP_URL; ?>images/setup_wizard.png" alt="setup wizard">
						</div>
					</div>

					<div class="tf-help-center-banner">
						<div class="tf-help-center-content">
							<h2><?php _e( "Help Center", "ultimate-addons-cf7" ); ?></h2>
							<p><?php _e( "To help you to get started, we put together the documentation, support link, videos and FAQs here.", "ultimate-addons-cf7" ); ?>
							</p>
						</div>
						<div class="tf-help-center-image">
							<img src="<?php // echo TF_ASSETS_APP_URL; ?>images/help-center.jpg" alt="HELP Center Image">
						</div>
					</div>

					<div class="tf-support-document">
						<div class="tf-single-support">
							<a href="https://themefic.com/docs/uacf7/" target="_blank">
								<img src="<?php // echo TF_ASSETS_APP_URL; ?>images/tf-documents.png" alt="Document">
								<h3><?php _e( "Documentation", "ultimate-addons-cf7" ); ?></h3>
								<span><?php _e( "Read More", "ultimate-addons-cf7" ); ?></span>
							</a>
						</div>
						<div class="tf-single-support">
							<a href="https://portal.themefic.com/support/" target="_blank">
								<img src="<?php // echo TF_ASSETS_APP_URL; ?>images/tf-mail.png" alt="Document">
								<h3><?php _e( "Email Support", "ultimate-addons-cf7" ); ?></h3>
								<span><?php _e( "Contact Us", "ultimate-addons-cf7" ); ?></span>
							</a>
						</div>

						<div class="tf-single-support">
							<a href="https://cf7addons.com" target="_blank">
								<img src="<?php // echo TF_ASSETS_APP_URL; ?>images/tf-comment.png" alt="Document">
								<h3><?php _e( "Live Chat", "ultimate-addons-cf7" ); ?></h3>
								<span><?php _e( "Chat Now", "ultimate-addons-cf7" ); ?></span>
							</a>
						</div>

						<div class="tf-single-support">
							<a href="https://www.youtube.com/playlist?list=PLY0rtvOwg0ylCl7NTwNHUPq-eY1qwUH_N" target="_blank">
								<img src="<?php // echo TF_ASSETS_APP_URL; ?>images/tf-tutorial.png" alt="Document">
								<h3><?php _e( "Video Tutorials", "ultimate-addons-cf7" ); ?></h3>
								<span><?php _e( "Watch Video", "ultimate-addons-cf7" ); ?></span>
							</a>
						</div>
					</div>

					<div class="tf-settings-faq">
						<h2><?php _e( "Common FAQs", "ultimate-addons-cf7" ); ?></h2>

						<div class="tf-accordion-wrapper">
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<i class="fas fa-angle-down"></i>
										<h4><?php _e( "What is UACF7? ", "ultimate-addons-cf7" ); ?></h4>
									</div>
									<div class="tf-faq-desc">
										<p>
											<?php _e( "", "ultimate-addons-cf7" ); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<i class="fas fa-angle-down"></i>
										<h4><?php _e( "How to install UACF7 ", "ultimate-addons-cf7" ); ?></h4>
									</div>
									<div class="tf-faq-desc">
										<p>
											<?php _e( "Please check our documentations", "ultimate-addons-cf7" ); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<i class="fas fa-angle-down"></i>
										<h4><?php _e( "Is Free version fully free or there is a gap? ", "ultimate-addons-cf7" ); ?>
										</h4>
									</div>
									<div class="tf-faq-desc">
										<p>
											<?php _e( "", "ultimate-addons-cf7" ); ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		public function uacf7_license_info_callback() {
			do_action( 'uacf7_license_info_pro_callback' );
		}

		/**
		 * Options Page
		 * @author Foysal
		 */
		public function tf_options_page() {

			// Retrieve an existing value from the database.
			$tf_option_value = get_option( $this->option_id );
			$current_page_url = $this->get_current_page_url();
			$query_string = $this->get_query_string( $current_page_url );

			// Set default values.
			if ( empty( $tf_option_value ) ) {
				$tf_option_value = array();
			}


			$ajax_save_class = 'tf-ajax-save';

			if ( ! empty( $this->option_sections ) ) :

				?>
				<div class="tf-setting-dashboard">
					<!-- dashboard-header-include -->
					<?php echo $this->tf_top_header(); ?>

					<div class="tf-option-wrapper tf-setting-wrapper">
						<form method="post" action="" class="tf-option-form <?php echo esc_attr( $ajax_save_class ) ?>"
							enctype="multipart/form-data">
							<!-- Body -->
							<input type="hidden" name="uacf7_current_page" value="uacf7_settings_page">
							<div class="tf-option">
								<div class="tf-admin-tab tf-option-nav">
									<?php
									$section_count = 0;
									// uacf7_print_r($this->pre_tabs);
									foreach ( $this->pre_tabs as $key => $section ) :
										if ( isset( $section['sub_section'] ) && $key != 'addons_settings' ) {

											$parent_tab_key = ! empty( $section['fields'] ) ? $key : array_key_first( $section['sub_section'] );
										} elseif ( $key == 'addons_settings' ) {
											continue;

										} else {
											$parent_tab_key = '';
										}

										?>
										<div
											class="tf-admin-tab-item<?php echo ! empty( $section['sub_section'] ) ? ' tf-has-submenu' : '' ?>">

											<a href="#<?php echo esc_attr( $parent_tab_key ); ?>"
												class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
												data-tab="<?php echo esc_attr( $parent_tab_key ) ?>">
												<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
												<?php echo $section['title']; ?>
											</a>

											<?php if ( ! empty( $section['sub_section'] ) ) : ?>
												<ul class="tf-submenu">
													<?php foreach ( $section['sub_section'] as $sub_key => $sub ) :
														if ( $sub_key == 'general_addons' || $sub_key == 'extra_fields_addons' || $sub_key == 'wooCommerce_integration' ) {
															continue;
														}
														?>

														<li>
															<a href="#<?php echo esc_attr( $sub_key ); ?>"
																class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
																data-tab="<?php echo esc_attr( $sub_key ) ?>">
																<span class="tf-tablinks-inner">
																	<?php echo ! empty( $sub['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $sub['icon'] ) . '"></i></span>' : ''; ?>
																	<?php echo $sub['title']; ?>
																</span>
															</a>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
										<?php $section_count++; endforeach; ?>
								</div>

								<div class="tf-tab-wrapper">
									<div class="tf-mobile-setting">
										<a href="#" class="tf-mobile-tabs"><i class="fa-solid fa-bars"></i></a>
									</div>
									<?php
									$content_count = 0;
									foreach ( $this->option_sections as $key => $section ) :
										if ( $key == 'general_addons' || $key == 'extra_fields_addons' || $key == 'wooCommerce_integration' ) {
											continue;
										}
										?>

										<div id="<?php echo esc_attr( $key ) ?>"
											class="tf-tab-content <?php echo $content_count == 0 ? 'active' : ''; ?>">

											<?php
											if ( ! empty( $section['fields'] ) ) :
												foreach ( $section['fields'] as $field ) :

													$default = isset( $field['default'] ) ? $field['default'] : '';
													$value = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

													$tf_option = new UACF7_Options();
													$tf_option->field( $field, $value, $this->option_id );

												endforeach;
											endif; ?>

										</div>
										<?php $content_count++; endforeach; ?>

									<!-- Footer -->
									<div class="tf-option-footer">
										<button type="submit"
											class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php _e( 'Save', 'ultimate-addons-cf7' ); ?></button>
									</div>
								</div>
							</div>
							<?php wp_nonce_field( 'uacf7_option_nonce_action', 'uacf7_option_nonce' ); ?>
						</form>
					</div>
					<?php
			endif;
		}

		/**
		 * Save Options
		 * @author Foysal
		 */
		public function save_options() {

			// Check if a nonce is valid.
			if ( ! isset( $_POST['uacf7_option_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['uacf7_option_nonce'] ) ), 'uacf7_option_nonce_action' ) ) {
				return;
			}

			//  Checked Currenct can save option
			$current_user = wp_get_current_user();
			$current_user_role = $current_user->roles[0];

			if ( $current_user_role !== 'administrator' && ! is_admin() ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}

			$option = get_option( $this->option_id );
			$option_request = ( ! empty( $_POST[ $this->option_id ] ) ) ? $_POST[ $this->option_id ] : array();
			$uacf7_current_page = ( ! empty( $_POST['uacf7_current_page'] ) ) ? $_POST['uacf7_current_page'] : '';

			if ( isset( $_POST['tf_import_option'] ) && ! empty( wp_unslash( trim( $_POST['tf_import_option'] ) ) ) ) {

				$tf_import_option = json_decode( wp_unslash( trim( $_POST['tf_import_option'] ) ), true );

				// $option_request = !empty($tf_import_option) && is_array($tf_import_option) ? $tf_import_option : $option_request;
				update_option( $this->option_id, $tf_import_option );
				return;
			}

			if ( $option && $option_request ) {
				$tf_option_value = $option;
			} else {
				$tf_option_value = array();
			}

			if ( ! empty( $option_request ) && ! empty( $this->option_sections ) ) {

				foreach ( $this->option_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {

								$fieldClass = 'UACF7_' . $field['type'];

								if ( $fieldClass == 'UACF7_tab' ) {
									$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';
									if ( $data == '' ) {
										$data = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : '';
									}
									foreach ( $field['tabs'] as $tab ) {
										foreach ( $tab['fields'] as $tab_fields ) {
											if ( $tab_fields['type'] == 'repeater' ) {
												foreach ( $tab_fields['fields'] as $key => $tab_field ) {
													if ( isset( $tab_field['validate'] ) && $tab_field['validate'] == 'no_space_no_special' ) {
														$sanitize_data_array = [];
														if ( ! empty( $data[ $tab_fields['id'] ] ) ) {
															foreach ( $data[ $tab_fields['id'] ] as $_key => $datum ) {
																//unique id 3 digit
																$unique_id = substr( uniqid(), -3 );
																$sanitize_data = sanitize_title( str_replace( ' ', '_', strtolower( $datum[ $tab_field['id'] ] ) ) );
																if ( in_array( $sanitize_data, $sanitize_data_array ) ) {
																	$sanitize_data = $sanitize_data . '_' . $unique_id;
																} else {
																	$sanitize_data_array[] = $sanitize_data;
																}

																$data[ $tab_fields['id'] ][ $_key ][ $tab_field['id'] ] = $sanitize_data;
															}
														}
													}
												}
											}
										}
									}
								} else {
									$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';
									if ( $data == '' && $uacf7_current_page == 'uacf7_addons_page' ) {
										$data = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : '';
									}
								}

								if ( $fieldClass != 'UACF7_file' ) {
									$data = $fieldClass == 'UACF7_repeater' || $fieldClass == 'UACF7_map' ? serialize( $data ) : $data;
									if ( $data == '' && $uacf7_current_page == 'uacf7_addons_page' ) {
										$data = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : '';
									}
								}
								if ( $fieldClass == 'UACF7_switch' ) {
									$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';
									if ( $data == '' && $uacf7_current_page != 'uacf7_addons_page' ) {
										$data = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : 0;
									}
									if ( isset( $field['save_empty'] ) && $uacf7_current_page == 'uacf7_settings_page' && $field['save_empty'] == true ) {
										$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';
									}
								}
								if ( $fieldClass == 'UACF7_textarea' ) {
									if ( $field['id'] == 'uacf7_booking_calendar_key' ) {
										if ( isset( $option_request[ $field['id'] ] ) && ! empty( $option_request[ $field['id'] ] ) ) {
											$option_request[ $field['id'] ] = stripslashes( $option_request[ $field['id'] ] );
											do_action( 'uacf7_booking_calendar_key_save', $option_request[ $field['id'] ] );
										}
									}
								}
								if ( isset( $_FILES ) && ! empty( $_FILES['file'] ) ) {
									$tf_upload_dir = wp_upload_dir();
									if ( ! empty( $tf_upload_dir['basedir'] ) ) {
										$tf_itinerary_fonts = $tf_upload_dir['basedir'] . '/itinerary-fonts';
										if ( ! file_exists( $tf_itinerary_fonts ) ) {
											wp_mkdir_p( $tf_itinerary_fonts );
										}
										$tf_fonts_extantions = array( 'application/octet-stream' );
										for ( $i = 0; $i < count( $_FILES['file']['name'] ); $i++ ) {
											if ( in_array( $_FILES['file']['type'][ $i ], $tf_fonts_extantions ) ) {
												$tf_font_filename = $_FILES['file']['name'][ $i ];
												move_uploaded_file( $_FILES['file']['tmp_name'][ $i ], $tf_itinerary_fonts . '/' . $tf_font_filename );
											}
										}
									}
								}

								if ( class_exists( $fieldClass ) ) {
									$_field = new $fieldClass( $field, $data, $this->option_id );
									$tf_option_value[ $field['id'] ] = $_field->sanitize();
								}

							}
						}
					}
				}
			}
			if ( ! empty( $tf_option_value ) ) {
				update_option( $this->option_id, $tf_option_value );
			} else {
				delete_option( $this->option_id );
			}
		}

		/*
		 * Ajax Save Options
		 * @author Foysal
		 */
		public function uacf7_ajax_save_options() {
			$response = [ 
				'status' => 'error',
				'message' => __( 'Something went wrong!', 'ultimate-addons-cf7' ),
			];

			if ( ! empty( $_POST['uacf7_option_nonce'] ) && wp_verify_nonce( $_POST['uacf7_option_nonce'], 'uacf7_option_nonce_action' ) ) {
				if ( isset( $_POST['tf_import_option'] ) && ! empty( wp_unslash( trim( $_POST['tf_import_option'] ) ) ) ) {

					$tf_import_option = json_decode( wp_unslash( trim( $_POST['tf_import_option'] ) ), true );
					if ( empty( $tf_import_option ) || ! is_array( $tf_import_option ) ) {
						$response = [ 
							'status' => 'error',
							'message' => __( 'Your imported data is not valid', 'tourfic' ),
						];
					} else {
						$this->save_options();
						$response = [ 
							'status' => 'success',
							'message' => __( 'Options imported successfully!', 'tourfic' ),
						];
					}
				} else {
					$this->save_options();
					$response = [ 
						'status' => 'success',
						'message' => __( 'Options saved successfully!', 'tourfic' ),
					];

				}
			}

			echo json_encode( $response );
			wp_die();
		}

		/*
		 * Get current page url
		 * @return string
		 * @author Foysal
		 */
		public function get_current_page_url() {
			$page_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			return $page_url;
		}

		/*
		 * Get query string from url
		 * @return array
		 * @author Foysal
		 */
		public function get_query_string( $url ) {
			$url_parts = parse_url( $url );
			parse_str( $url_parts['query'], $query_string );

			return $query_string;
		}
	}
}
