<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_STAR_RATING {
	/*
	 * Construct function
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_script' ) );
		add_action( 'wpcf7_init', array( $this, 'add_shortcodes' ) );
		add_action( 'wpcf7_swv_create_schema', array( $this, 'uacf7_swv_add_checkbox_rules' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'tag_generator' ) );
	}

	/*
	 * Enqueue scripts
	 */
	public function enqueue_frontend_script() {
		wp_enqueue_style( 'uacf7-star-rating-style', UACF7_ADDONS . '/star-rating/assets/css/star-rating.css' );
		wp_enqueue_style( 'uacf7-fontawesome', UACF7_ADDONS . '/star-rating/assets/css/all.css' );
	}


	/*
	 * Star rating Validation
	 */
	public function uacf7_swv_add_checkbox_rules( $schema, $contact_form ) {
		$tags = $contact_form->scan_form_tags( array(
			'type' => array( 'uacf7_star_rating*', ),
		) );

		foreach ( $tags as $tag ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'required', array(
					'field' => $tag->name,
					'error' => wpcf7_get_message( 'invalid_required' ),
				) )
			);
		}
	}



	/*
	 * Create form tag: uacf7_star_rating
	 */
	public function add_shortcodes() {

		wpcf7_add_form_tag( array( 'uacf7_star_rating', 'uacf7_star_rating*' ), array( $this, 'uacf7_star_rating_cb' ), true );

	}

	/*
	 * Field: Post title
	 */
	public function uacf7_star_rating_cb( $tag ) {

		ob_start();
		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$class .= ' uacf7-rating';

		$atts['class'] = $class;

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}
		if ( $validation_error ) {
			$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
				$tag->name
			);
		}

		$rating_style = $tag->values;


		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$atts = wpcf7_format_atts( $atts );

		$selected = ! empty( $tag->get_option( 'selected', '', true ) ) ? $tag->get_option( 'selected', '', true ) : '5';
		$selected = $tag->get_option( 'selected', '', true );

		$star1 = ! empty( $tag->get_option( 'star1', '', true ) ) ? $tag->get_option( 'star1', '', true ) : '1';
		$star2 = ! empty( $tag->get_option( 'star2', '', true ) ) ? $tag->get_option( 'star2', '', true ) : '2';
		$star3 = ! empty( $tag->get_option( 'star3', '', true ) ) ? $tag->get_option( 'star3', '', true ) : '3';
		$star4 = ! empty( $tag->get_option( 'star4', '', true ) ) ? $tag->get_option( 'star4', '', true ) : '4';
		$star5 = ! empty( $tag->get_option( 'star5', '', true ) ) ? $tag->get_option( 'star5', '', true ) : '5';

		$rating_icon = '<i class="fas fa-star"></i>';

		if ( function_exists( 'uacf7_rating_icon' ) ) {

			if ( ! empty( uacf7_rating_icon( $tag ) ) ) {
				$rating_icon = uacf7_rating_icon( $tag );
			}

		} else {

			$get_icon = $tag->get_option( 'icon', '', true );

			switch ( $get_icon ) {
				case 'star1':
					$rating_icon = '<i class="far fa-star"></i>';
					break;
				case 'star2':
					$rating_icon = '✪';
					break;
			}
		}
		?>
		<span data-name="<?php echo esc_attr( $tag->name ); ?>"
			class="wpcf7-form-control-wrap <?php echo esc_attr( $tag->name ); ?>">
			<span <?php echo $atts; ?>>
				<label>
					<input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( $star1 ); ?>"
						<?php checked( $selected, '1', true ); ?> />
					<span class="icon"><?php echo $rating_icon; ?></span>
				</label>
				<label>
					<input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( $star2 ); ?>"
						<?php checked( $selected, '2', true ); ?> />
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
				</label>
				<label>
					<input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( $star3 ); ?>"
						<?php checked( $selected, '3', true ); ?> />
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
				</label>
				<label>
					<input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( $star4 ); ?>"
						<?php checked( $selected, '4', true ); ?> />
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
				</label>
				<label>
					<input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( $star5 ); ?>"
						<?php checked( $selected, '5', true ); ?> />
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
					<span class="icon"><?php echo $rating_icon; ?></span>
				</label>

			</span>
		</span>
		<span>
			<?php
			echo $validation_error;
			?>
		</span>

		<?php
		$default_star_style = ob_get_clean();
		return apply_filters( 'uacf7_star_rating_style_pro_feature', $default_star_style, $tag );

	}
	/*
	 * Generate tag
	 */
	public function tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_star_rating',
			__( 'Star Rating', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_star_rating' ],
			array( 'version' => '2' )
		);

	}

	static function tg_pane_star_rating( $contact_form, $options ) {

		$field_types = array(
			'uacf7_star_rating' => array(
				'display_name' => __( 'Star Rating', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate a Star Rating Field.', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		?>

		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_star_rating']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_star_rating']['description'],
				array(
					'a' => array( 'href' => true ),
					'strong' => array(),
				),
				array( 'http', 'https' )
			);

			echo $description;
			?></p>
			<div class="uacf7-doc-notice">
				<?php echo sprintf(
					__( 'Confused? Check our Documentation on %1s.', 'ultimate-addons-cf7' ),
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-star-rating-field/" target="_blank">Star Rating</a>'
				); ?>
			</div>
		</header>
		<div class="control-box uacf7-control-box">

			<?php

			$tgg->print( 'field_type', array(
				'with_required' => true,
				'select_options' => array(
					'uacf7_star_rating' => $field_types['uacf7_star_rating']['display_name'],
				),
			) );

			$tgg->print( 'field_name' );
			?>

			<fieldset>
				<legend>
					<?php echo esc_html__( 'Rating Icon', 'ultimate-addons-cf7' ); ?>
				</legend>

				<label for="star1">
					<input id="star1" data-tag-part="option" data-tag-option="icon:" name="icon" type="radio" value="star1" />
					<?php echo esc_html( 'Star 1' ); ?>
				</label>

				<label for="star2">
					<input id="star2" data-tag-part="option" data-tag-option="icon:" name="icon" type="radio" value="star2" />
					<?php echo esc_html( 'Star 2' ); ?>
				</label>

				<?php ob_start(); ?>
				<label>
					<input data-tag-part="option" data-tag-option="icon:" type="radio" disabled />
					<?php echo esc_html( 'Heart' ); ?>
					<a href="https://cf7addons.com/preview/star-rating/pro" style="color:red">
						(Pro)
					</a>
				</label>

				<label>
					<input data-tag-part="option" data-tag-option="icon:" type="radio" disabled />
					<?php echo esc_html( 'Thumbs Up' ); ?>
					<a href="https://cf7addons.com/preview/star-rating/pro" style="color:red">
						(Pro)
					</a>
				</label>

				<label>
					<input data-tag-part="option" data-tag-option="icon:" type="radio" disabled />
					<?php echo esc_html( 'Smile' ); ?>
					<a href="https://cf7addons.com/preview/star-rating/pro" style="color:red">
						(Pro)
					</a>
				</label>

				<label>
					<input data-tag-part="option" data-tag-option="icon:" type="radio" disabled />
					<?php echo esc_html( 'Ok' ); ?>
					<a href="https://cf7addons.com/preview/star-rating/pro" style="color:red">
						(Pro)
					</a>
				</label>

				<legend>
					<?php _e( 'Icon Class', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input data-tag-part="option" data-tag-option="class:" id="tag-generator-panel-text-star-class" type="text"
					placeholder="e.g: fa fa-star" disabled />
				<a href="https://cf7addons.com/preview/star-rating/pro" style="color:red">
					(Pro)
				</a>

				<?php $icon_field = ob_get_clean();
				echo apply_filters( 'uacf7_star_rating_tg_field', $icon_field );
				?>
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Star Rating Style', 'ultimate-addons-cf7' ); ?>
				</legend>

				<?php ob_start() ?>
				<select data-tag-part="value" name="values" disabled id="tag-generator-panel-range-style">
					<option value="default">
						<?php _e( 'Default', 'ultimate-addons-cf7' ); ?>
					</option>
				</select>
				<a href="https://cf7addons.com/preview/star-rating/pro" style="color:red">(Pro)</a>

				<?php
				$rating_style = ob_get_clean();
				echo apply_filters( 'uacf7_star_rating_style_field', $rating_style );
				?>
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Default Star Rating', 'ultimate-addons-cf7' ); ?>
				</legend>

				<input type="number" data-tag-part="option" data-tag-option="selected:" name="selected"
					id="tag-generator-panel-text-selected" placeholder="5" />

				<p>
					<?php _e( 'Change the values of star. Default value: 1,2,3,4,5', 'ultimate-addons-cf7' ); ?>
				</p>
				</td>
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Star 1', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="text" data-tag-part="option" data-tag-option="star1:" name="star1"
					id="tag-generator-panel-text-star1">
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Star 2', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="text" data-tag-part="option" data-tag-option="star2:" name="star2"
					id="tag-generator-panel-text-star2">
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Star 3', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="text" data-tag-part="option" data-tag-option="star3:" name="star3"
					id="tag-generator-panel-text-star3">
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Star 4', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="text" data-tag-part="option" data-tag-option="star4:" name="star4"
					id="tag-generator-panel-text-star3">
			</fieldset>

			<fieldset>
				<legend>
					<?php _e( 'Star 5', 'ultimate-addons-cf7' ); ?>
				</legend>
				<input type="text" data-tag-part="option" data-tag-option="star5:" name="star5"
					id="tag-generator-panel-text-star4">
			</fieldset>

			<?php $tgg->print( 'class_attr' ); ?>
		</div>

		<footer class="insert-box">
			<?php
			$tgg->print( 'insert_box_content' );

			$tgg->print( 'mail_tag_tip' );
			?>
		</footer>

		<?php
	}
}

new UACF7_STAR_RATING();