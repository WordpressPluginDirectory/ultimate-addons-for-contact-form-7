<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UACF7_editor' ) ) {
	class UACF7_editor extends UACF7_Fields {

        public function __construct( $field, $value = '', $settings_id = '', $parent_field = '', $section_key = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field, $section_key  );
		}

		public function render() {
            $tf_editor_unique_id = str_replace( array("[","]"),"_",esc_attr( $this->field_name() ) );
            
            $parent_class = ( ! empty( $this->parent_field ) ) ? 'parent_wp_editor' : 'wp_editor'; 
            $parent_class = ( isset( $this->field['wp_editor'] ) ) ? 'wp_editor' : $parent_class ;  
        ?>
        <div class="tf-field-textarea">
            <textarea name="<?php echo $this->field_name(); ?>" id="<?php echo $tf_editor_unique_id; ?>" class="<?php echo esc_attr( $parent_class )  ?> tf_wp_editor" cols="30" data-count-id=""><?php echo $this->value; ?></textarea>
        </div>
       <?php
		} 
        public function sanitize() {
			return wp_kses_post($this->value);
		}
	}
}