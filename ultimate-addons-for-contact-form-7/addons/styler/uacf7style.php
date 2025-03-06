<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_uacf7style {

	/*
	 * Construct function
	 */
	public function __construct() {
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'uacf7_properties' ), 10, 2 );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_styler' ), 13, 2 );
	}



	// Add Placeholder Options
	public function uacf7_post_meta_options_styler( $value, $post_id ) {
		$redirection = apply_filters( 'uacf7_post_meta_options_styler_pro', $data = array(
			'title' => __( 'Form Styler', 'ultimate-addons-cf7' ),
			'icon' => 'fa-solid fa-mortar-pestle',
			'checked_field' => 'uacf7_enable_form_styles',
			'fields' => array(
				'styler_heading' => array(
					'id' => 'styler_heading',
					'type' => 'heading',
					'label' => __( 'Single Form Styler Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
						__( 'Style your entire form without any CSS coding, including colors, margins, button styles, and font sizes. These options overrides Global Form Styler Settings. See Demo %1s.', 'ultimate-addons-cf7' ),
						'<a href="https://cf7addons.com/preview/contact-form-7-style-addon/" target="_blank">Example</a>'
					)
				),
				'styler_docs' => array(
					'id' => 'styler_docs',
					'type' => 'notice',
					'style' => 'success',
					'content' => sprintf(
						__( 'Confused? Check our Documentation on  %1s and %2s.', 'ultimate-addons-cf7' ),
						'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-style/" target="_blank">Single Form Styler</a>',
						'<a href="https://themefic.com/docs/uacf7/pro-addons/global-form-styler-for-contact-form-7/" target="_blank">Global Form Styler</a>'
					)
				),
				'uacf7_enable_form_styles' => array(
					'id' => 'uacf7_enable_form_styles',
					'type' => 'switch',
					'label' => __( 'Enable Form Styles', 'ultimate-addons-cf7' ),
					'label_on' => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default' => false
				),
				'styler_heading_label' => array(
					'id' => 'styler_heading_label',
					'type' => 'heading',
					'label' => __( 'Label Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'All changes on this section are applicable to the "Label" items. Ensure each label is enclosed within a <label></label> tag.', 'ultimate-addons-cf7' ), //Sydur fix the html here
				),
				'uacf7_uacf7style_label_color_option' => array(
					'id' => 'uacf7_uacf7style_label_color_option',
					'type' => 'color',
					'label' => __( 'Color Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Change the text and background colors of the labels.', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => true,
					'inline' => true,
					'colors' => array(
						'uacf7_uacf7style_label_color' => 'Color',
						'uacf7_uacf7style_label_background_color' => 'Background Color',
					),
				),
				'uacf7_uacf7style_label_font_style' => array(
					'id' => 'uacf7_uacf7style_label_font_style',
					'type' => 'select',
					'label' => __( 'Font Style', 'ultimate-addons-cf7' ),
					'options' => array(
						'normal' => 'Normal',
						'italic' => "Italic",
					),
					'field_width' => 50,
				),
				'uacf7_uacf7style_label_font_weight' => array(
					'id' => 'uacf7_uacf7style_label_font_weight',
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
				'uacf7_uacf7style_label_font_size' => array(
					'id' => 'uacf7_uacf7style_label_font_size',
					'type' => 'number',
					'label' => __( 'Font Size (in px)', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Placeholder Font Size (in px)', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_uacf7style_label_font_family' => array(
					'id' => 'uacf7_uacf7style_label_font_family',
					'type' => 'text',
					'label' => __( 'Font Name ', 'ultimate-addons-cf7' ),
					'subtitle' => __( " E.g. Roboto, sans-serif (Do not add special characters like '' or ;) ", "ultimate-addons-cf7" ),
					'placeholder' => __( 'Enter Placeholder Font Name ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'styler_heading_label_padding' => array(
					'id' => 'styler_heading_label_padding',
					'type' => 'heading',
					'class' => 'heading-inner',
					'title' => __( 'Padding (in px)', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_label_padding_top' => array(
					'id' => 'uacf7_uacf7style_label_padding_top',
					'type' => 'number',
					'label' => __( 'Top', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Top', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_label_padding_right' => array(
					'id' => 'uacf7_uacf7style_label_padding_right',
					'type' => 'number',
					'label' => __( 'Right', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Right', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_label_padding_bottom' => array(
					'id' => 'uacf7_uacf7style_label_padding_bottom',
					'type' => 'number',
					'label' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_label_padding_left' => array(
					'id' => 'uacf7_uacf7style_label_padding_left',
					'type' => 'number',
					'label' => __( 'Left', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Left', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'styler_heading_label_margin' => array(
					'id' => 'styler_heading_label_margin',
					'class' => 'heading-inner',
					'type' => 'heading',
					'title' => __( 'Margin (in px)', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em). ', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_label_margin_top' => array(
					'id' => 'uacf7_uacf7style_label_margin_top',
					'type' => 'number',
					'label' => __( 'Top', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Top', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_label_margin_right' => array(
					'id' => 'uacf7_uacf7style_label_margin_right',
					'type' => 'number',
					'label' => __( 'Right', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Right', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_label_margin_bottom' => array(
					'id' => 'uacf7_uacf7style_label_margin_bottom',
					'type' => 'number',
					'label' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_label_margin_left' => array(
					'id' => 'uacf7_uacf7style_label_margin_left',
					'type' => 'number',
					'label' => __( 'Left', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Left', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'styler_heading_input' => array(
					'id' => 'styler_heading_label',
					'type' => 'heading',
					'label' => __( 'Input Field Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'All modifications in this section are applicable to "Input" fields, such as text, textarea, dropdown, email, etc.', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_input_color_option' => array(
					'id' => 'uacf7_uacf7style_input_color_option',
					'type' => 'color',
					'label' => __( 'Color Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Change the text and background colors of the input fields.', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => true,
					'inline' => true,
					'colors' => array(
						'uacf7_uacf7style_input_color' => 'Color',
						'uacf7_uacf7style_input_background_color' => 'Background Color',
					),
				),
				'uacf7_uacf7style_input_font_style' => array(
					'id' => 'uacf7_uacf7style_input_font_style',
					'type' => 'select',
					'label' => __( 'Font Style', 'ultimate-addons-cf7' ),
					'options' => array(
						'normal' => 'Normal',
						'italic' => "Italic",
					),
					'field_width' => 50,
				),
				'uacf7_uacf7style_input_font_weight' => array(
					'id' => 'uacf7_uacf7style_input_font_weight',
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
				'uacf7_uacf7style_input_font_size' => array(
					'id' => 'uacf7_uacf7style_input_font_size',
					'type' => 'number',
					'label' => __( 'Font Size (in px)', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Input Font Size', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_uacf7style_input_font_family' => array(
					'id' => 'uacf7_uacf7style_input_font_family',
					'type' => 'text',
					'label' => __( 'Font Name ', 'ultimate-addons-cf7' ),
					'subtitle' => __( " E.g. Roboto, sans-serif (Do not add special characters like '' or ;) ", "ultimate-addons-cf7" ),
					'placeholder' => __( 'Enter Input Font Name ', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_uacf7style_input_height' => array(
					'id' => 'uacf7_uacf7style_input_height',
					'type' => 'number',
					'label' => __( 'Input Height (in px)', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Input Height', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),

				'uacf7_uacf7style_textarea_input_height' => array(
					'id' => 'uacf7_uacf7style_textarea_input_height',
					'type' => 'number',
					'label' => __( 'Input (Textarea) Height (in px)', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Textarea Height', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'styler_heading_input_padding' => array(
					'id' => 'styler_heading_input_padding',
					'type' => 'heading',
					'class' => 'heading-inner',
					'title' => __( 'Padding (in px)', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_input_padding_top' => array(
					'id' => 'uacf7_uacf7style_input_padding_top',
					'type' => 'number',
					'label' => __( 'Top', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Top', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_padding_right' => array(
					'id' => 'uacf7_uacf7style_input_padding_right',
					'type' => 'number',
					'label' => __( 'Right', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Right', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_padding_bottom' => array(
					'id' => 'uacf7_uacf7style_input_padding_bottom',
					'type' => 'number',
					'label' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_padding_left' => array(
					'id' => 'uacf7_uacf7style_input_padding_left',
					'type' => 'number',
					'label' => __( 'Left', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Left', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'styler_heading_input_margin' => array(
					'id' => 'styler_heading_input_margin',
					'class' => 'heading-inner',
					'type' => 'heading',
					'title' => __( 'Margin (in px)', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em). ', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_input_margin_top' => array(
					'id' => 'uacf7_uacf7style_input_margin_top',
					'type' => 'number',
					'label' => __( 'Top', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Top', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_margin_right' => array(
					'id' => 'uacf7_uacf7style_input_margin_right',
					'type' => 'number',
					'label' => __( 'Right', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Right', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_margin_bottom' => array(
					'id' => 'uacf7_uacf7style_input_margin_bottom',
					'type' => 'number',
					'label' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_margin_left' => array(
					'id' => 'uacf7_uacf7style_input_margin_left',
					'type' => 'number',
					'label' => __( 'Left', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Left', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'styler_heading_input_border' => array(
					'id' => 'styler_heading_input_border',
					'class' => 'heading-inner',
					'type' => 'heading',
					'title' => __( 'Border ', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_input_border_width' => array(
					'id' => 'uacf7_uacf7style_input_border_width',
					'type' => 'number',
					'label' => __( 'Border Width (in px)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter input border width', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16(Do not add px or em ). ', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_border_style' => array(
					'id' => 'uacf7_uacf7style_input_border_style',
					'type' => 'select',
					'label' => __( 'Border Style ', 'ultimate-addons-cf7' ),
					'options' => array(
						'solid' => "Solid",
						'dotted' => "Dotted",
						'dashed' => "Dashed",
						'double' => "Double",
						'none' => 'None',
					),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_border_radius' => array(
					'id' => 'uacf7_uacf7style_input_border_radius',
					'type' => 'number',
					'label' => __( 'Border Radius (in px)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter input border radius', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16(Do not add px or em ). ', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_input_border_color' => array(
					'id' => 'uacf7_uacf7style_input_border_color',
					'type' => 'color',
					'field_width' => 25,
					'label' => __( 'Border Color', 'ultimate-addons-cf7' ),
					// 'subtitle'     => __( 'Customize Placeholder Color Options', 'ultimate-addons-cf7' ), 
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
					// 'colors' => array(
					//     'uacf7_uacf7style_label_color' => 'Color',
					//     'uacf7_uacf7style_label_background_color' => 'Background Color', 
					// ), 
				),
				'styler_heading_button' => array(
					'id' => 'styler_heading_label',
					'type' => 'heading',
					'label' => __( 'Submit Button Options', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'All modifications in this section are applicable to the "Submit" button of the form.', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_btn_color_option' => array(
					'id' => 'uacf7_uacf7style_btn_color_option',
					'type' => 'color',
					'label' => __( 'Button Color', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => true,
					'inline' => true,
					'colors' => array(
						'uacf7_uacf7style_btn_color' => 'Color',
						'uacf7_uacf7style_btn_color_hover' => 'Color (hover)',
						'uacf7_uacf7style_btn_background_color' => 'Background Color',
						'uacf7_uacf7style_btn_background_color_hover' => 'Background Color (hover)',
					),
				),
				'uacf7_uacf7style_btn_font_size' => array(
					'id' => 'uacf7_uacf7style_btn_font_size',
					'type' => 'number',
					'label' => __( 'Font Size (in px)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Button Font Size', 'ultimate-addons-cf7' ),
					'content' => __( 'E.g. 16 (Do not add px or em ).', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_uacf7style_btn_font_style' => array(
					'id' => 'uacf7_uacf7style_btn_font_style',
					'type' => 'select',
					'label' => __( 'Font Style', 'ultimate-addons-cf7' ),
					'options' => array(
						'normal' => 'Normal',
						'italic' => "Italic",
					),
					'field_width' => 50,
				),
				'uacf7_uacf7style_btn_font_weight' => array(
					'id' => 'uacf7_uacf7style_btn_font_weight',
					'type' => 'select',
					'label' => __( 'Font Weight', 'ultimate-addons-cf7' ),
					'options' => array(
						'normal' => 'Normal / 400',
						'300' => "300",
						'500' => "500",
						'700' => "700",
						'900' => "900",
					),
					'field_width' => 50,
				),
				'uacf7_uacf7style_btn_width' => array(
					'id' => 'uacf7_uacf7style_btn_width',
					'type' => 'text',
					'label' => __( 'Width (in px or %)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter input border width', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 100px or 100%.', 'ultimate-addons-cf7' ),
					'field_width' => 50,
				),
				'uacf7_uacf7style_btn_border_style' => array(
					'id' => 'uacf7_uacf7style_btn_border_style',
					'type' => 'select',
					'label' => __( 'Border Style ', 'ultimate-addons-cf7' ),
					'options' => array(
						'none' => 'None',
						'dotted' => "Dotted",
						'dashed' => "Dashed",
						'solid' => "Solid",
						'double' => "Double",
					),
					'field_width' => 33,
				),
				'uacf7_uacf7style_btn_border_width' => array(
					'id' => 'uacf7_uacf7style_btn_border_width',
					'type' => 'number',
					'label' => __( 'Border Width (in px)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Button border width', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em ).', 'ultimate-addons-cf7' ),
					'field_width' => 33,
				),
				'uacf7_uacf7style_btn_border_radius' => array(
					'id' => 'uacf7_uacf7style_btn_border_radius',
					'type' => 'number',
					'label' => __( 'Border Radius (in px)', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Enter Button border radius', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em ).', 'ultimate-addons-cf7' ),
					'field_width' => 33,
				),
				'uacf7_uacf7style_btn_border_color' => array(
					'id' => 'uacf7_uacf7style_btn_border_color',
					'type' => 'color',
					'label' => __( 'Border Color', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => false,
					// 'colors' => array(
					//     'uacf7_uacf7style_btn_color' => 'Color',
					//     'uacf7_uacf7style_btn_color_hover' => 'Color (hover)', 
					//     'uacf7_uacf7style_btn_background_color' => 'Background Color (hover)', 
					//     'uacf7_uacf7style_btn_background_color_hover' => 'Background Color (hover)', 
					// ),  
					'field_width' => 50,
				),
				'uacf7_uacf7style_btn_border_color_hover' => array(
					'id' => 'uacf7_uacf7style_btn_border_color_hover',
					'type' => 'color',
					'label' => __( 'Border Color (Hover)', 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					// 'default' => '#ffffff',
					'multiple' => false,
					'inline' => true,
					// 'colors' => array(
					//     'uacf7_uacf7style_btn_color' => 'Color',
					//     'uacf7_uacf7style_btn_color_hover' => 'Color (hover)', 
					//     'uacf7_uacf7style_btn_background_color' => 'Background Color (hover)', 
					//     'uacf7_uacf7style_btn_background_color_hover' => 'Background Color (hover)', 
					// ),  
					'field_width' => 50,
				),
				'uacf7_uacf7style_btn_padding' => array(
					'id' => 'uacf7_uacf7style_btn_padding',
					'type' => 'heading',
					'class' => 'heading-inner',
					'title' => __( 'Padding (in px)', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em).', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_btn_padding_top' => array(
					'id' => 'uacf7_uacf7style_btn_padding_top',
					'type' => 'number',
					'label' => __( 'Top', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Top', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_padding_right' => array(
					'id' => 'uacf7_uacf7style_btn_padding_right',
					'type' => 'number',
					'label' => __( 'Right', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Right', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_padding_bottom' => array(
					'id' => 'uacf7_uacf7style_btn_padding_bottom',
					'type' => 'number',
					'label' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_padding_left' => array(
					'id' => 'uacf7_uacf7style_btn_padding_left',
					'type' => 'number',
					'label' => __( 'Left', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Left', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_margin' => array(
					'id' => 'uacf7_uacf7style_btn_margin',
					'class' => 'heading-inner',
					'type' => 'heading',
					'title' => __( 'Margin (in px)', 'ultimate-addons-cf7' ),
					'content' => __( ' E.g. 16 (Do not add px or em). ', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_btn_margin_top' => array(
					'id' => 'uacf7_uacf7style_btn_margin_top',
					'type' => 'number',
					'label' => __( 'Top', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Top', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_margin_right' => array(
					'id' => 'uacf7_uacf7style_btn_margin_right',
					'type' => 'number',
					'label' => __( 'Right', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Right', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_margin_bottom' => array(
					'id' => 'uacf7_uacf7style_btn_margin_bottom',
					'type' => 'number',
					'label' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Bottom', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_btn_margin_left' => array(
					'id' => 'uacf7_uacf7style_btn_margin_left',
					'type' => 'number',
					'label' => __( 'Left', 'ultimate-addons-cf7' ),
					'placeholder' => __( 'Left', 'ultimate-addons-cf7' ),
					'field_width' => 25,
				),
				'uacf7_uacf7style_ua_custom_header' => array(
					'id' => 'uacf7_uacf7style_ua_custom_header',
					'type' => 'heading',
					'label' => __( 'Custom CSS', 'ultimate-addons-cf7' ),
				),
				'uacf7_uacf7style_ua_custom_css' => array(
					'id' => 'uacf7_uacf7style_ua_custom_css',
					'type' => 'code_editor',

				),
				// array(
				//     'id' => 'tf-editor',
				//     'type' => 'editor',
				//     'label' => 'Enter your label',
				//     'subtitle' => 'Enter your subtitle',
				//     'description' => 'Enter your description',
				//     'class' => 'tf-field-class',
				// ) 
			),
		), $post_id );
		$value['styler'] = $redirection;
		return $value;
	}

	public function uacf7_properties( $properties, $cfform ) {

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			$form = $properties['form'];
			$form_meta = uacf7_get_form_option( $cfform->id(), 'styler' );

			$form_styles = isset( $form_meta['uacf7_enable_form_styles'] ) ? $form_meta['uacf7_enable_form_styles'] : false;

			if ( $form_styles == true ) :

				ob_start();

				$label_color = $form_meta['uacf7_uacf7style_label_color_option']['uacf7_uacf7style_label_color'];
				$label_background_color = $form_meta['uacf7_uacf7style_label_color_option']['uacf7_uacf7style_label_background_color'];
				$label_font_size = $form_meta['uacf7_uacf7style_label_font_size'];
				$label_font_family = $form_meta['uacf7_uacf7style_label_font_family'];
				$label_font_style = $form_meta['uacf7_uacf7style_label_font_style'];
				$label_font_weight = $form_meta['uacf7_uacf7style_label_font_weight'];
				$label_padding_top = $form_meta['uacf7_uacf7style_label_padding_top'];
				$label_padding_right = $form_meta['uacf7_uacf7style_label_padding_right'];
				$label_padding_bottom = $form_meta['uacf7_uacf7style_label_padding_bottom'];
				$label_padding_left = $form_meta['uacf7_uacf7style_label_padding_left'];
				$label_margin_top = $form_meta['uacf7_uacf7style_label_margin_top'];
				$label_margin_right = $form_meta['uacf7_uacf7style_label_margin_right'];
				$label_margin_bottom = $form_meta['uacf7_uacf7style_label_margin_bottom'];
				$label_margin_left = $form_meta['uacf7_uacf7style_label_margin_left'];

				$input_color = $form_meta['uacf7_uacf7style_input_color_option']['uacf7_uacf7style_input_color'];
				$input_background_color = $form_meta['uacf7_uacf7style_input_color_option']['uacf7_uacf7style_input_background_color'];
				$input_font_size = $form_meta['uacf7_uacf7style_input_font_size'];
				$input_font_family = $form_meta['uacf7_uacf7style_input_font_family'];
				$input_font_style = $form_meta['uacf7_uacf7style_input_font_style'];
				$input_font_weight = $form_meta['uacf7_uacf7style_input_font_weight'];
				$input_height = $form_meta['uacf7_uacf7style_input_height'];
				$input_border_width = $form_meta['uacf7_uacf7style_input_border_width'];
				$input_border_color = $form_meta['uacf7_uacf7style_input_border_color'];
				$input_border_style = $form_meta['uacf7_uacf7style_input_border_style'];
				$input_border_radius = $form_meta['uacf7_uacf7style_input_border_radius'];
				$textarea_input_height = $form_meta['uacf7_uacf7style_textarea_input_height'];
				$input_padding_top = $form_meta['uacf7_uacf7style_input_padding_top'];
				$input_padding_right = $form_meta['uacf7_uacf7style_input_padding_right'];
				$input_padding_bottom = $form_meta['uacf7_uacf7style_input_padding_bottom'];
				$input_padding_left = $form_meta['uacf7_uacf7style_input_padding_left'];
				$input_margin_top = $form_meta['uacf7_uacf7style_input_margin_top'];
				$input_margin_right = $form_meta['uacf7_uacf7style_input_margin_right'];
				$input_margin_bottom = $form_meta['uacf7_uacf7style_input_margin_bottom'];
				$input_margin_left = $form_meta['uacf7_uacf7style_input_margin_left'];

				$btn_color = $form_meta['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_color'];
				$btn_background_color = $form_meta['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_background_color'];
				$btn_font_size = $form_meta['uacf7_uacf7style_btn_font_size'];
				$btn_font_style = $form_meta['uacf7_uacf7style_btn_font_style'];
				$btn_font_weight = $form_meta['uacf7_uacf7style_btn_font_weight'];
				$btn_width = $form_meta['uacf7_uacf7style_btn_width'];
				$btn_border_color = $form_meta['uacf7_uacf7style_btn_border_color'];
				$btn_border_style = $form_meta['uacf7_uacf7style_btn_border_style'];
				$btn_border_radius = $form_meta['uacf7_uacf7style_btn_border_radius'];
				$btn_border_width = $form_meta['uacf7_uacf7style_btn_border_width'];
				$btn_color_hover = $form_meta['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_color_hover'];
				$btn_background_color_hover = $form_meta['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_background_color_hover'];
				$btn_border_color_hover = $form_meta['uacf7_uacf7style_btn_border_color_hover'];
				$btn_padding_top = $form_meta['uacf7_uacf7style_btn_padding_top'];
				$btn_padding_right = $form_meta['uacf7_uacf7style_btn_padding_right'];
				$btn_padding_bottom = $form_meta['uacf7_uacf7style_btn_padding_bottom'];
				$btn_padding_left = $form_meta['uacf7_uacf7style_btn_padding_left'];
				$btn_margin_top = $form_meta['uacf7_uacf7style_btn_margin_top'];
				$btn_margin_right = $form_meta['uacf7_uacf7style_btn_margin_right'];
				$btn_margin_bottom = $form_meta['uacf7_uacf7style_btn_margin_bottom'];
				$btn_margin_left = $form_meta['uacf7_uacf7style_btn_margin_left'];
				$ua_custom_css = $form_meta['uacf7_uacf7style_ua_custom_css'];
				?>
				<style>
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> label {
						<?php
						// Color
						if ( ! empty( $label_color ) ) {
							echo 'color: ' . esc_attr( $label_color ) . ';';
						}

						// Background color
						if ( ! empty( $label_background_color ) ) {
							echo 'background-color: ' . esc_attr( $label_background_color ) . ';';
						}

						// Font size
						if ( ! empty( $label_font_size ) ) {
							echo 'font-size: ' . esc_attr( $label_font_size ) . 'px;';
						}

						// Font family
						if ( ! empty( $label_font_family ) ) {
							echo 'font-family: ' . esc_attr( $label_font_family ) . ';';
						}

						// Font style
						if ( ! empty( $label_font_style ) ) {
							echo 'font-style: ' . esc_attr( $label_font_style ) . ';';
						}

						// Font weight
						if ( ! empty( $label_font_weight ) ) {
							echo 'font-weight: ' . esc_attr( $label_font_weight ) . ';';
						}

						// Padding
						if ( ! empty( $label_padding_top ) ) {
							echo 'padding-top: ' . esc_attr( $label_padding_top ) . 'px;';
						}
						if ( ! empty( $label_padding_right ) ) {
							echo 'padding-right: ' . esc_attr( $label_padding_right ) . 'px;';
						}
						if ( ! empty( $label_padding_bottom ) ) {
							echo 'padding-bottom: ' . esc_attr( $label_padding_bottom ) . 'px;';
						}
						if ( ! empty( $label_padding_left ) ) {
							echo 'padding-left: ' . esc_attr( $label_padding_left ) . 'px;';
						}

						// Margin
						if ( ! empty( $label_margin_top ) ) {
							echo 'margin-top: ' . esc_attr( $label_margin_top ) . 'px;';
						}
						if ( ! empty( $label_margin_right ) ) {
							echo 'margin-right: ' . esc_attr( $label_margin_right ) . 'px;';
						}
						if ( ! empty( $label_margin_bottom ) ) {
							echo 'margin-bottom: ' . esc_attr( $label_margin_bottom ) . 'px;';
						}
						if ( ! empty( $label_margin_left ) ) {
							echo 'margin-left: ' . esc_attr( $label_margin_left ) . 'px;';
						}
						?>
					}

					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="email"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="number"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="password"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="search"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="tel"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="text"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="url"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="date"],
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> select,
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> textarea {
						<?php
						// Color
						if ( ! empty( $input_color ) ) {
							echo 'color: ' . esc_attr( $input_color ) . ';';
						}

						// Background color
						if ( ! empty( $input_background_color ) ) {
							echo 'background-color: ' . esc_attr( $input_background_color ) . ';';
						}

						// Font size
						if ( ! empty( $input_font_size ) ) {
							echo 'font-size: ' . esc_attr( $input_font_size ) . 'px;';
						}

						// Font family
						if ( ! empty( $input_font_family ) ) {
							echo 'font-family: ' . esc_attr( $input_font_family ) . ';';
						}

						// Font style
						if ( ! empty( $input_font_style ) ) {
							echo 'font-style: ' . esc_attr( $input_font_style ) . ';';
						}

						// Font weight
						if ( ! empty( $input_font_weight ) ) {
							echo 'font-weight: ' . esc_attr( $input_font_weight ) . ';';
						}

						// Height
						if ( ! empty( $input_height ) ) {
							echo 'height: ' . esc_attr( $input_height ) . 'px;';
						}

						// Border
						if ( ! empty( $input_border_width ) ) {
							echo 'border-width: ' . esc_attr( $input_border_width ) . 'px;';
						}
						if ( ! empty( $input_border_color ) ) {
							echo 'border-color: ' . esc_attr( $input_border_color ) . ';';
						}
						if ( ! empty( $input_border_style ) ) {
							echo 'border-style: ' . esc_attr( $input_border_style ) . ';';
						}
						if ( ! empty( $input_border_radius ) ) {
							echo 'border-radius: ' . esc_attr( $input_border_radius ) . 'px;';
						}

						// Padding
						if ( ! empty( $input_padding_top ) ) {
							echo 'padding-top: ' . esc_attr( $input_padding_top ) . 'px;';
						}
						if ( ! empty( $input_padding_right ) ) {
							echo 'padding-right: ' . esc_attr( $input_padding_right ) . 'px;';
						}
						if ( ! empty( $input_padding_bottom ) ) {
							echo 'padding-bottom: ' . esc_attr( $input_padding_bottom ) . 'px;';
						}
						if ( ! empty( $input_padding_left ) ) {
							echo 'padding-left: ' . esc_attr( $input_padding_left ) . 'px;';
						}

						// Margin
						if ( ! empty( $input_margin_top ) ) {
							echo 'margin-top: ' . esc_attr( $input_margin_top ) . 'px;';
						}
						if ( ! empty( $input_margin_right ) ) {
							echo 'margin-right: ' . esc_attr( $input_margin_right ) . 'px;';
						}
						if ( ! empty( $input_margin_bottom ) ) {
							echo 'margin-bottom: ' . esc_attr( $input_margin_bottom ) . 'px;';
						}
						if ( ! empty( $input_margin_left ) ) {
							echo 'margin-left: ' . esc_attr( $input_margin_left ) . 'px;';
						}
						?>
					}

					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> .wpcf7-radio span,
					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> .wpcf7-checkbox span {
						<?php
						// Color
						if ( ! empty( $input_color ) ) {
							echo 'color: ' . esc_attr( $input_color ) . ';';
						}

						// Font size
						if ( ! empty( $input_font_size ) ) {
							echo 'font-size: ' . esc_attr( $input_font_size ) . 'px;';
						}

						// Font family
						if ( ! empty( $input_font_family ) ) {
							echo 'font-family: ' . esc_attr( $input_font_family ) . ';';
						}

						// Font style
						if ( ! empty( $input_font_style ) ) {
							echo 'font-style: ' . esc_attr( $input_font_style ) . ';';
						}

						// Font weight
						if ( ! empty( $input_font_weight ) ) {
							echo 'font-weight: ' . esc_attr( $input_font_weight ) . ';';
						}
						?>
					}

					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> textarea {
						<?php
						// Height
						if ( ! empty( $textarea_input_height ) ) {
							echo 'height: ' . esc_attr( $textarea_input_height ) . 'px;';
						}
						?>
					}

					.wpcf7-form-control-wrap select {
						width: 100%;
					}

					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="submit"] {
						<?php
						// Color
						if ( ! empty( $btn_color ) ) {
							echo 'color: ' . esc_attr( $btn_color ) . ';';
						}

						// Background color
						if ( ! empty( $btn_background_color ) ) {
							echo 'background-color: ' . esc_attr( $btn_background_color ) . ';';
						}

						// Font size
						if ( ! empty( $btn_font_size ) ) {
							echo 'font-size: ' . esc_attr( $btn_font_size ) . 'px;';
						}

						// Font family
						if ( ! empty( $input_font_family ) ) {
							echo 'font-family: ' . esc_attr( $input_font_family ) . ';';
						}

						// Font style
						if ( ! empty( $btn_font_style ) ) {
							echo 'font-style: ' . esc_attr( $btn_font_style ) . ';';
						}

						// Font weight
						if ( ! empty( $btn_font_weight ) ) {
							echo 'font-weight: ' . esc_attr( $btn_font_weight ) . ';';
						}

						// Border
						if ( ! empty( $btn_border_width ) ) {
							echo 'border-width: ' . esc_attr( $btn_border_width ) . 'px;';
						}
						if ( ! empty( $btn_border_color ) ) {
							echo 'border-color: ' . esc_attr( $btn_border_color ) . ';';
						}
						if ( ! empty( $btn_border_style ) ) {
							echo 'border-style: ' . esc_attr( $btn_border_style ) . ';';
						}
						if ( ! empty( $btn_border_radius ) ) {
							echo 'border-radius: ' . esc_attr( $btn_border_radius ) . 'px;';
						}

						// Width
						if ( ! empty( $btn_width ) ) {
							echo 'width: ' . esc_attr( $btn_width ) . ';';
						}

						// Padding
						if ( ! empty( $btn_padding_top ) ) {
							echo 'padding-top: ' . esc_attr( $btn_padding_top ) . 'px;';
						}
						if ( ! empty( $btn_padding_right ) ) {
							echo 'padding-right: ' . esc_attr( $btn_padding_right ) . 'px;';
						}
						if ( ! empty( $btn_padding_bottom ) ) {
							echo 'padding-bottom: ' . esc_attr( $btn_padding_bottom ) . 'px;';
						}
						if ( ! empty( $btn_padding_left ) ) {
							echo 'padding-left: ' . esc_attr( $btn_padding_left ) . 'px;';
						}

						// Margin
						if ( ! empty( $btn_margin_top ) ) {
							echo 'margin-top: ' . esc_attr( $btn_margin_top ) . 'px;';
						}
						if ( ! empty( $btn_margin_right ) ) {
							echo 'margin-right: ' . esc_attr( $btn_margin_right ) . 'px;';
						}
						if ( ! empty( $btn_margin_bottom ) ) {
							echo 'margin-bottom: ' . esc_attr( $btn_margin_bottom ) . 'px;';
						}
						if ( ! empty( $btn_margin_left ) ) {
							echo 'margin-left: ' . esc_attr( $btn_margin_left ) . 'px;';
						}
						?>
					}

					.uacf7-uacf7style-<?php esc_attr_e( $cfform->id() ); ?> input[type="submit"]:hover {
						<?php
						// Hover color
						if ( ! empty( $btn_color_hover ) ) {
							echo 'color: ' . esc_attr( $btn_color_hover ) . ';';
						}

						// Hover background color
						if ( ! empty( $btn_background_color_hover ) ) {
							echo 'background-color: ' . esc_attr( $btn_background_color_hover ) . ';';
						}

						// Hover border color
						if ( ! empty( $btn_border_color_hover ) ) {
							echo 'border-color: ' . esc_attr( $btn_border_color_hover ) . ';';
						}
						?>
					}

					<?php echo $ua_custom_css ?>
				</style>

				<?php echo '<div class="uacf7-uacf7style uacf7-uacf7style-' . esc_attr( $cfform->id() ) . '">' . $form . '</div>';
				$properties['form'] = ob_get_clean();
			endif;
		}

		return $properties;
	}

	public function old_uacf7_properties( $properties, $cfform ) {
		wp_register_style( 'uacf7-single-form-styler', UACF7_URL . 'addons/styler/css/uacf7-single-form-styler.css', [], null );

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$form = $properties['form'];
			$form_meta = uacf7_get_form_option( $cfform->id(), 'styler' );
			$form_styles = $form_meta['uacf7_enable_form_styles'] ?? false;
			$ua_custom_css = isset($form_meta['uacf7_uacf7style_ua_custom_css']) ? $form_meta['uacf7_uacf7style_ua_custom_css'] : '';

			if ( $form_styles ) {
				$css = $this->generate_dynamic_css( $cfform->id(), $form_meta );

				// If custom css is added
				if(!empty($ua_custom_css)) {
					$com_css = $ua_custom_css . $css;
				} else {
					$com_css = $css;
				}

				wp_add_inline_style( 'uacf7-single-form-styler', $com_css );
				wp_enqueue_style( 'uacf7-single-form-styler' );
			}
		}

		return $properties;
	}

	/**
	 * Generate Dynamic CSS based on form meta.
	 */
	private function generate_dynamic_css( $form_id, $form_meta ) {
		$selectors = [ 
			'label' => [ 
				'color' => $form_meta['uacf7_uacf7style_label_color_option']['uacf7_uacf7style_label_color'] ?? null,
				'background-color' => $form_meta['uacf7_uacf7style_label_color_option']['uacf7_uacf7style_label_background_color'] ?? null,
				'font-size' => $form_meta['uacf7_uacf7style_label_font_size'] ? $form_meta['uacf7_uacf7style_label_font_size'] . 'px' : null,
				'font-family' => $form_meta['uacf7_uacf7style_label_font_family'] ?? null,
				'font-style' => $form_meta['uacf7_uacf7style_label_font_style'] ?? null,
				'font-weight' => $form_meta['uacf7_uacf7style_label_font_weight'] ?? null,

				...array_filter(array_values($this->generate_spacing_css($form_meta, 'label_padding', 'padding'))),
				...array_filter(array_values($this->generate_spacing_css( $form_meta, 'label_margin', 'margin' ))),
			],
			'.wpcf7-form-control:not(.wpcf7-submit)' => [ 
				'color' => $form_meta['uacf7_uacf7style_input_color_option']['uacf7_uacf7style_input_color'] ?? null,
				'background-color' => $form_meta['uacf7_uacf7style_input_color_option']['uacf7_uacf7style_input_background_color'] ?? null,
				'font-size' => $form_meta['uacf7_uacf7style_input_font_size'] ? $form_meta['uacf7_uacf7style_input_font_size'] . 'px' : null,
				'font-family' => $form_meta['uacf7_uacf7style_input_font_family'] ?? null,
				'font-style' => $form_meta['uacf7_uacf7style_input_font_style'] ?? null,
				'font-weight' => $form_meta['uacf7_uacf7style_input_font_weight'] ?? null,
				'border' => $this->generate_border_css( $form_meta, 'input_border' ),
				'border-radius' => $form_meta['uacf7_uacf7style_input_border_radius'] ? $form_meta['uacf7_uacf7style_input_border_radius'] . 'px' : null,

				...array_filter(array_values($this->generate_spacing_css( $form_meta, 'input_padding', 'padding' ))),
				...array_filter(array_values($this->generate_spacing_css( $form_meta, 'input_margin', 'margin' ))),
			],
			'.wpcf7-submit' => [ 
				'color' => $form_meta['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_color'] ?? null,
				'background-color' => $form_meta['uacf7_uacf7style_btn_color_option']['uacf7_uacf7style_btn_background_color'] ?? null,
				'font-size' => $form_meta['uacf7_uacf7style_btn_font_size'] ? $form_meta['uacf7_uacf7style_btn_font_size'] . 'px' : null,
				'border' => $this->generate_border_css( $form_meta, 'btn_border' ),
				'border-radius' => $form_meta['uacf7_uacf7style_btn_border_radius'] ? $form_meta['uacf7_uacf7style_btn_border_radius'] . 'px' : null,
				
				...array_filter(array_values($this->generate_spacing_css( $form_meta, 'btn_padding', 'padding' ))),
				...array_filter(array_values($this->generate_spacing_css( $form_meta, 'btn_margin','margin' ))),
			],
		];

		$css = '';
		foreach ( $selectors as $selector => $properties ) {
			$css .= $this->build_css_rule( ".uacf7-uacf7style-{$form_id} {$selector}", $properties );
		}

		return $css;
	}

	/**
	 * Generate CSS for spacing properties (padding or margin).
	 */
	private function generate_spacing_css($form_meta, $key_prefix, $property) {
		$css = [];
		$sides = ['top', 'right', 'bottom', 'left'];
		foreach ($sides as $side) {
			$value = $form_meta["uacf7_uacf7style_{$key_prefix}_{$side}"] ?? null;
			if ($value !== null) {
				$css["{$property}-{$side}"] = "{$value}px";
			}
		}
		return $css;
	}

	/**
	 * Build CSS rule from properties.
	 */
	private function build_css_rule( $selector, $properties ) {
		$rules = '';
		foreach ( $properties as $property => $value ) {
			if (is_array($value)) {
				// If $value is an array, convert it to a string
				$value = implode(' ', $value);
			}
			if (!empty($value)) {
				$rules .= "{$property}: {$value};";
			}
		}
		return $rules ? "{$selector} { {$rules} }" : '';
	}
	

	/**
	 * Generate CSS for border properties.
	 */
	private function generate_border_css( $form_meta, $key_prefix ) {
		$style = $form_meta[ "uacf7_uacf7style_{$key_prefix}_style" ] ?? null;
		$width = $form_meta[ "uacf7_uacf7style_{$key_prefix}_width" ] ? $form_meta[ "uacf7_uacf7style_{$key_prefix}_width" ] . 'px' : null;
		$color = $form_meta[ "uacf7_uacf7style_{$key_prefix}_color" ] ?? null;

		return $style && $width && $color ? "{$width} {$style} {$color}" : null;
	}


}
new UACF7_uacf7style();
