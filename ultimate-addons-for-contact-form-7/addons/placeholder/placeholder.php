<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_Placeholder {

	/*
	 * Construct function
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_placeholder_style' ) );
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'uacf7_properties' ), 10, 2 );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_placeholder' ), 12, 2 );
	}

	// public function admin_enqueue_placeholder_styles() {
	//     wp_enqueue_style( 'uacf7-placeholder-style', UACF7_URL . 'addons/', array(), null, true );
	// } 

	public function enqueue_placeholder_style() {
		wp_enqueue_style( 'uacf7-placeholder', UACF7_ADDONS . '/placeholder/css/placeholder-style.css' );
		wp_enqueue_script( 'uacf7-placeholder-script', UACF7_ADDONS . '/placeholder/js/color-pickr.js', array( 'jquery', 'wp-color-picker' ), '', true );
	}

	// Add Placeholder Options
	public function uacf7_post_meta_options_placeholder( $value, $post_id ) {
		$redirection = apply_filters( 'uacf7_post_meta_options_placeholder_pro', $data = array(
			'title' => __( 'Placeholder Styler', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-italic',
            'checked_field'   => 'uacf7_enable_placeholder_styles',
			'fields' => array(
				'placeholder_heading' => array(
					'id' => 'placeholder_heading',
					'type' => 'heading', 
					'label' => __( 'Placeholder Styler Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
                        __( 'Style form placeholders, like text color and background color, without writing any CSS. See Demo %1s.', 'ultimate-addons-cf7' ),
                         '<a href="https://cf7addons.com/preview/contact-form-7-placeholder-styling/" target="_blank">Example</a>'
                    )
				),
				'placeholder_docs' => array(
					'id'      => 'placeholder_docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => sprintf( 
                        __( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
                        '<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-placeholder-styling/" target="_blank">Placeholder Styling</a>'
                    )
				),
				'uacf7_enable_placeholder_styles' => array(
					'id' => 'uacf7_enable_placeholder_styles',
					'type' => 'switch',
					'label' => __( ' Enable Placeholder Styling', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false
				),
				'placeholder_styles_form_options_heading' => array(
                    'id'        => 'placeholder_styles_form_options_heading',
                    'type'      => 'heading',
                    'label'     => __( 'Placeholder Styles Option ', 'ultimate-addons-cf7' ),
                ),
				'uacf7_placeholder_color_option' => array(
					'id' => 'uacf7_placeholder_color_option',
					'type' => 'color',
					'label' => __( 'Color Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Change the Text Color & Background Color of Placeholders.', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					'default' => '#ffffff',
					'multiple' => true,
					'inline' => true,
					'colors' => array(
						'uacf7_placeholder_color' => 'Color',
						'uacf7_placeholder_background_color' => 'Background Color',
					),
				),
				'uacf7_placeholder_fontstyle' => array(
					'id' => 'uacf7_placeholder_fontstyle',
					'type' => 'select',
					'label' => __( 'Font Style', 'ultimate-addons-cf7' ),
					'options' => array(
						'normal' => 'Normal',
						'italic' => "Italic",
					),
					'field_width' => 50,
				),
				'uacf7_placeholder_fontweight' => array(
					'id' => 'uacf7_placeholder_fontweight',
					'type' => 'select',
					'label' => __( 'Font Weight ', 'ultimate-addons-cf7' ),
					'options' => array(
						'normal' => 'Normal / 400',
						'300' => "300",
						'500' => "500",
						'700' => "700",
						'900' => "900",
					),
					'field_width' => 50,
				),
				'uacf7_placeholder_fontsize' => array(
					'id' => 'uacf7_placeholder_fontsize',
					'type' => 'number',
					'label' => __( 'Font Size (in px)', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Placeholder Font Size (in px)', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_placeholder_fontfamily' => array(
					'id' => 'uacf7_placeholder_fontfamily',
					'type' => 'text',
					'label' => __( 'Font Name ', 'ultimate-addons-cf7' ),
					'subtitle' => __( " E.g. Roboto, sans-serif (Do not add special characters like '' or ;) ", "ultimate-addons-cf7" ),
					'placeholder' => __( 'Enter Placeholder Font Name ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				// array(
				//     'id' => 'uacf7_placeholder_notice',
				//     'type' => 'notice',
				//     'content' => __( " Need more placeholder or other options? Let us know here . ", "ultimate-addons-cf7" ),  
				//     'class' => 'tf-field-class',   
				//     'notice' => 'info',
				// )  
			),
		), $post_id );
		$value['placeholder'] = $redirection;
		return $value;
	}




	public function uacf7_properties( $properties, $cfform ) {

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			$form = $properties['form'];

			$form_meta = uacf7_get_form_option( $cfform->id(), 'placeholder' ); 

			$placeholder_styles = isset( $form_meta['uacf7_enable_placeholder_styles'] ) ? $form_meta['uacf7_enable_placeholder_styles'] : false;

			if ( $placeholder_styles == true ) :

				ob_start();

				$fontfamily = $form_meta['uacf7_placeholder_fontfamily'];
				$fontsize = $form_meta['uacf7_placeholder_fontsize'];
				$fontstyle = $form_meta['uacf7_placeholder_fontstyle'];
				$fontweight = $form_meta['uacf7_placeholder_fontweight'];
				$color = isset( $form_meta['uacf7_placeholder_color_option'] ) ? $form_meta['uacf7_placeholder_color_option']['uacf7_placeholder_color'] : '';
				$background_color = isset( $form_meta['uacf7_placeholder_color_option'] ) ? $form_meta['uacf7_placeholder_color_option']['uacf7_placeholder_background_color'] : '';
				?>
				<style>
					.uacf7-form-<?php esc_attr_e( $cfform->id() ); ?>
					::placeholder {
						color:
							<?php echo esc_attr_e( $color ); ?>
						;
						background-color:
							<?php echo esc_attr_e( $background_color ); ?>
						;
						font-size:
							<?php echo esc_attr_e( $fontsize ) . 'px'; ?>
						;
						font-family:
							<?php echo esc_attr_e( $fontfamily ); ?>
						;
						font-style:
							<?php echo esc_attr_e( $fontstyle ); ?>
						;
						font-weight:
							<?php echo esc_attr_e( $fontweight ); ?>
						;
					}

					.uacf7-form-

					<?php esc_attr_e( $cfform->id() ); ?>
					::-webkit-input-placeholder {
						/* Edge */
						color:
							<?php echo esc_attr_e( $color ); ?>
						;
						background-color:
							<?php echo esc_attr_e( $background_color ); ?>
						;
						font-size:
							<?php echo esc_attr_e( $fontsize ) . 'px'; ?>
						;
						font-family:
							<?php echo esc_attr_e( $fontfamily ); ?>
						;
						font-style:
							<?php echo esc_attr_e( $fontstyle ); ?>
						;
						font-weight:
							<?php echo esc_attr_e( $fontweight ); ?>
						;
					}

					.uacf7-form-

					<?php esc_attr_e( $cfform->id() ); ?>
					:-ms-input-placeholder {
						/* Internet Explorer 10-11 */
						color:
							<?php echo esc_attr_e( $color ); ?>
						;
						background-color:
							<?php echo esc_attr_e( $background_color ); ?>
						;
						font-size:
							<?php echo esc_attr_e( $fontsize ) . 'px'; ?>
						;
						font-family:
							<?php echo esc_attr_e( $fontfamily ); ?>
						;
						font-style:
							<?php echo esc_attr_e( $fontstyle ); ?>
						;
						font-weight:
							<?php echo esc_attr_e( $fontweight ); ?>
						;
					}
				</style>
				<?php
				echo $form;
				$properties['form'] = ob_get_clean();

			endif;
		}

		return $properties;
	}

}
new UACF7_Placeholder();