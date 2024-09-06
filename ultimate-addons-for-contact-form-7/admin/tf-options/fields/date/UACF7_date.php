<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UACF7_date' ) ) {
	class UACF7_date extends UACF7_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '', $section_key = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field, $section_key  );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				// 'format'      => 'Y-m-d',
				'range'       => false,
				'multiple'    => false,
				'minDate'     => '',
				'label_from'  => esc_html__( 'From', 'ultimate-addons-cf7' ),
				'label_to'    => esc_html__( 'To', 'ultimate-addons-cf7' ),
				'placeholder' => esc_html__( 'Select Date', 'ultimate-addons-cf7' ),
			) );

			$value = wp_parse_args( $this->value, array(
				'from' => '',
				'to'   => '',
			) );

			$format      = ( ! empty( $args['format'] ) ) ? $args['format'] : 'Y-m-d';
			$range       = ( ! empty( $args['range'] ) ) ? $args['range'] : false;
			$multiple    = ( ! empty( $args['multiple'] ) ) ? $args['multiple'] : false;
			$placeholder = ( ! empty( $args['placeholder'] ) ) ? $args['placeholder'] : esc_html__( 'Select Date', 'ultimate-addons-cf7' );
            $minDate     = ( ! empty( $args['minDate'] ) ) ? $args['minDate'] : '';

			if ( $range ): ?>
                <div class="tf-date-range">
                    <div class="tf-date-from">
                        <label for="" class="tf-field-label"><?php echo esc_html( $args['label_from'] ) ?></label>
                        <div class="" style="position:relative;">
                            <input type="text" name="<?php echo esc_attr( $this->field_name() ); ?>[from]" placeholder="<?php echo esc_attr( $placeholder ) ?>" value="<?php echo esc_attr( $value['from'] ); ?>"
                                   class="flatpickr " data-format="<?php echo esc_attr( $format ); ?>" <?php echo $this->field_attributes() ?> data-min-date="<?php echo esc_attr( $minDate ); ?>"/>
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                    <div class="tf-date-to">
                        <label for="" class="tf-field-label"><?php echo esc_html( $args['label_to'] ) ?></label>
                        <div class="" style="position:relative;">
                            <input type="text" name="<?php echo esc_attr( $this->field_name() ); ?>[to]" placeholder="<?php echo esc_attr( $placeholder ) ?>" value="<?php echo esc_attr( $value['to'] ); ?>"
                                   class="flatpickr " data-format="<?php echo esc_attr( $format ); ?>" <?php echo $this->field_attributes() ?> data-min-date="<?php echo esc_attr( $minDate ); ?>"/>
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                </div>
			<?php else: ?>
                <input type="text" name="<?php echo esc_attr( $this->field_name() ); ?>" placeholder="<?php echo esc_attr( $placeholder ) ?>" value="<?php echo esc_attr( $this->value ); ?>"
                       class="flatpickr " data-format="<?php echo esc_attr( $format ); ?>" data-multiple="<?php echo esc_attr( $multiple ); ?>" <?php echo $this->field_attributes() ?> data-min-date="<?php echo esc_attr( $minDate ); ?>"/>
                <i class="fa-solid fa-calendar-days"></i>
			<?php
			endif;
		}
		public function sanitize() {
			return $this->value;
		}

	}
}