<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UACF7_code_editor' ) ) {
	class UACF7_code_editor extends UACF7_Fields {

    public $version = '5.65.15';
    public $cdn_url = 'https://cdn.jsdelivr.net/npm/codemirror@';

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '',  $section_key = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field,  $section_key  );
      $this->enqueue();
      
		}

		public function render() {
            $default_settings = array(
                'tabSize'       => 2,
                'lineNumbers'   => true,
                'theme'         => 'monokai',
                'mode'          => 'css',
                'cdnURL'        => $this->cdn_url . $this->version,
              );

            $settings = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
            $settings = wp_parse_args( $settings, $default_settings );

        ?>
        <div class="tf-field-textarea tf-field-code-editor">
            <?php
                echo '<textarea name="'. esc_attr( $this->field_name() ) .'"'. $this->field_attributes() .' data-editor="'. esc_attr( json_encode( $settings ) ) .'">'. $this->value .'</textarea>';
            ?>

        </div>
       <?php
		}
        
    public function enqueue() {

        $page = ( ! empty( $_GET[ 'page' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'page' ] ) ) : '';
  
        // Do not loads CodeMirror in revslider page.
        if ( in_array( $page, array( 'revslider' ) ) ) { return; }
  
        if ( ! wp_script_is( 'tf-codemirror' ) ) {
          wp_enqueue_script( 'tf-codemirror', esc_url( $this->cdn_url . $this->version .'/lib/codemirror.min.js' ), array(), $this->version, true );
          wp_enqueue_script( 'tf-codemirror-loadmode', esc_url( $this->cdn_url . $this->version .'/addon/mode/loadmode.min.js' ), array( 'tf-codemirror' ), $this->version, true );
        }
  
        if ( ! wp_style_is( 'tf-codemirror' ) ) {
          wp_enqueue_style( 'tf-codemirror', esc_url( $this->cdn_url . $this->version .'/lib/codemirror.min.css' ), array(), $this->version );
        }
  
    }

    public function sanitize() {
      return wp_kses_post($this->value);
    }

	}
}