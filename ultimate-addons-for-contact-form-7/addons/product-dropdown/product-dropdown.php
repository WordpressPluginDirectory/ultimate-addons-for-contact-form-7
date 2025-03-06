<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_PRODUCT_DROPDOWN {

	private $hidden_fields = array();
	/*
	 * Construct function
	 */
	public function __construct() {
		add_action( 'wpcf7_init', array( $this, 'add_shortcodes' ) );
		add_action( 'admin_init', array( $this, 'tag_generator' ) );
		add_filter( 'wpcf7_validate_uacf7_product_dropdown', array( $this, 'wpcf7_product_dropdown_validation_filter' ), 10, 2 );
		add_filter( 'wpcf7_validate_uacf7_product_dropdown*', array( $this, 'wpcf7_product_dropdown_validation_filter' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ) );
	}

	public function admin_enqueue_script() {

		wp_enqueue_script( 'uacf7-product-dropdown', UACF7_ADDONS . '/product-dropdown/assets/admin-script.js', array( 'jquery' ), null, true );
	}


	/*
	 * Form tag
	 */
	public function add_shortcodes() {

		wpcf7_add_form_tag( array( 'uacf7_product_dropdown', 'uacf7_product_dropdown*' ),
			array( $this, 'tag_handler_callback' ), array( 'name-attr' => true ) );
	}

	public function tag_handler_callback( $tag ) {

		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->name;
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$multiple = $tag->has_option( 'multiple' );
		$display_price = $tag->has_option( 'display_price' );

		if ( $tag->has_option( 'size' ) ) {
			$size = $tag->get_option( 'size', 'int', true );
			if ( $size ) {
				$atts['size'] = $size;
			} elseif ( $multiple ) {
				$atts['size'] = 4;
			} else {
				$atts['size'] = 1;
			}
		}




		if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
		}

		$values = $tag->values;

		$default_choice = $tag->get_default_option( null, array(
			'multiple' => $multiple,
		) );

		$hangover = wpcf7_get_hangover( $tag->name );

		if ( $tag->has_option( 'product_by:id' ) ) {

			$product_by = 'id';

		} elseif ( $tag->has_option( 'product_by:category' ) ) {

			$product_by = 'category';

		} elseif ( $tag->has_option( 'product_by:tag' ) ) {

			$product_by = 'tag';

		} else {
			$product_by = '';
		}





		/** Product Sorting By Feature */

		$query_array = [ 
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		];


		$new_args = [

		];


		/** If Date Selected  */

		// Default Sorting by Date from Woocommerce


		/** If ASC Selected */
		if ( $tag->has_option( 'order_by:asc' ) ) {
			$asc_args = [ 
				'orderby' => 'title',
				'order' => 'ASC'
			];

			$new_args = array_merge( $new_args, $asc_args );
		}

		/** If DSC Selected */

		if ( $tag->has_option( 'order_by:dsc' ) ) {
			$asc_args = [ 
				'orderby' => 'title',
				'order' => 'DSC'
			];

			$new_args = array_merge( $new_args, $asc_args );
		}


		$very_last_array = array_merge( $query_array, $new_args );



		$args = apply_filters( 'uacf7_product_dropdown_query', $very_last_array
			, $values, $product_by );



		$products = new WP_Query( $args );
		if ( $multiple ) {
			$atts['multiple'] = apply_filters( 'uacf7_multiple_attribute', '' );
			$atts['uacf7-select2-type'] = 'multiple';
		}
		$dropdown = '<option value="">-Select-</option>';
		while ( $products->have_posts() ) {
			$products->the_post();

			if ( $hangover ) {
				$selected = in_array( get_the_title(), (array) $hangover, true );
			} else {
				$selected = in_array( get_the_title(), (array) $default_choice, true );
			}

			$item_atts = array(
				'value' => get_the_title(),
				'selected' => $selected ? 'selected' : '',
				'product-id' => get_the_id(),

			);

			$item_atts = wpcf7_format_atts( $item_atts );

			$label = get_the_title();

			$dropdown .= sprintf( '<option %1$s>%2$s</option>',
				$item_atts, esc_html( $label ) );
		}
		wp_reset_postdata();


		if ( $tag->has_option( 'layout:select2' ) ) {
			$atts['uacf7-select2-type'] = 'single';

		}
		if ( $tag->has_option( 'layout:select2' ) && $multiple ) {
			$atts['uacf7-select2-type'] = 'multiple';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
		$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

		$atts = wpcf7_format_atts( $atts );

		$dropdown = sprintf(
			'<div class="%1$s"><span class="wpcf7-form-control-wrap %1$s"  data-name="%1$s"><select %2$s>%3$s</select></span><span>%4$s</span></div>',
			sanitize_html_class( $tag->name ), $atts, $dropdown, $validation_error
		);

		if ( $tag->has_option( 'layout:grid' ) ) { // Grid Layout
			$tag_name = $tag->name;
			$html = apply_filters( 'uacf7_dorpdown_grid', $dropdown, $multiple, $products, $hangover, $default_choice, $tag_name, $validation_error, $display_price );
		} else {
			$html = $dropdown;
		}

		return $html;
	}



	public function wpcf7_product_dropdown_validation_filter( $result, $tag ) {
		$name = $tag->name;

		if ( isset( $_POST[ $name ] )
			and is_array( $_POST[ $name ] ) ) {
			foreach ( $_POST[ $name ] as $key => $value ) {
				if ( '' === $value ) {
					unset( $_POST[ $name ][ $key ] );
				}
			}
		}

		$empty = ! isset( $_POST[ $name ] ) || empty( $_POST[ $name ] ) && '0' !== $_POST[ $name ];

		if ( $tag->is_required() and $empty ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/*
	 * Generate tag - conditional
	 */
	public function tag_generator() {

		$tag_generator = WPCF7_TagGenerator::get_instance();

		$tag_generator->add(
			'uacf7_product_dropdown',
			__( 'Product Dropdown', 'ultimate-addons-cf7' ),
			[ $this, 'tg_pane_product_dropdown' ],
			array( 'version' => '2' )
		);

	}

	static function tg_pane_product_dropdown( $contact_form, $options ) {

		$field_types = array(
			'uacf7_product_dropdown' => array(
				'display_name' => __( 'Product Dropdown', 'ultimate-addons-cf7' ),
				'heading' => __( 'Generate Product Dropdown', 'ultimate-addons-cf7' ),
				'description' => __( '', 'ultimate-addons-cf7' ),
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) || version_compare( get_option( 'woocommerce_db_version' ), '2.5', '<' ) ) {
			$woo_activation = false;
		} else {
			$woo_activation = true;
		}
		?>

		<header class="description-box">
			<h3><?php
			echo esc_html( $field_types['uacf7_product_dropdown']['heading'] );
			?></h3>

			<p><?php
			$description = wp_kses(
				$field_types['uacf7_product_dropdown']['description'],
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
					__( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
					'<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-woocommerce/" target="_blank">Product Dropdown</a>'
				); ?>
			</div>
		</header>

		<div class="control-box uacf7-control-box">
			<?php

			$tgg->print( 'field_type', array(
				'with_required' => true,
				'select_options' => array(
					'uacf7_product_dropdown' => $field_types['uacf7_product_dropdown']['display_name'],
				),
			) );

			$tgg->print( 'field_name' );

			?>

			<fieldset>
				<legend>
					<?php echo esc_html( __( 'Field Option', 'ultimate-addons-cf7' ) ); ?>
				</legend>

				<div class="uacf7_field_wraping">
					<div>
						<?php ob_start(); ?>
						<input type="checkbox" data-tag-part="option" data-tag-option="" disabled />

						<?php echo esc_attr( __( 'Allow multiple selections ', 'ultimate-addons-cf7' ) ); ?>

						<a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">
							(Pro)
						</a>

						<?php $multiple_attr = ob_get_clean(); ?>
						<?php
						/*
						 * Tag generator field after field type
						 */
						echo apply_filters( 'uacf7_tag_generator_multiple_select_field', $multiple_attr );
						?>
					</div>

					<div>
						<?php ob_start(); ?>
						<input type="checkbox" data-tag-part="option" data-tag-option="" disabled />
						<?php echo esc_attr( __( 'Display Total of Selected Product Price', 'ultimate-addons-cf7' ) ); ?>

						<a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>
						<?php $display_price = ob_get_clean(); ?>

						<?php
						/*
						 * Tag generator field after field type
						 */
						echo apply_filters( 'uacf7_tag_generator_display_price_field', $display_price );
						?>
					</div>
				</div>

			</fieldset>

			<fieldset>
				<?php ob_start(); ?>
				<legend>
					<?php echo esc_html( __( 'Show Product By', 'ultimate-addons-cf7' ) ); ?>
					<a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>
				</legend>

				<input id="byID" name="product_by" disabled type="radio" value="id" checked />
				<?php echo esc_html( __( ' Product ID', 'ultimate-addons-cf7' ) ); ?>

				<input id="byCategory" name="product_by" disabled type="radio" value="category" />
				<?php echo esc_html( __( 'Category', 'ultimate-addons-cf7' ) ); ?>

				<input id="byTag" name="product_by" disabled type="radio" value="tag" />
				<?php echo esc_html( __( 'Tag', 'ultimate-addons-cf7' ) ); ?>

				<?php
				$product_by = ob_get_clean();
				echo apply_filters( 'uacf7_tag_generator_product_by_field', $product_by );
				?>
			</fieldset>

			<fieldset>
				<?php ob_start(); ?>
				<legend>
					<?php echo esc_attr( __( 'Product Order By', 'ultimate-addons-cf7' ) ); ?>
				</legend>

				<label for="byDate">
					<input id="byDate" name="order_by" class="" disabled type="radio" value="" checked>
					<?php echo esc_html( __( ' Date (by Default)', 'ultimate-addons-cf7' ) ); ?></label>

				<label for="byASC">
					<input id="byASC" name="order_by" class="" disabled type="radio" value="asc">
					<?php echo esc_html( __( 'ASC', 'ultimate-addons-cf7' ) ); ?>
				</label>

				<label for="byDSC">
					<input id="byDSC" name="order_by" class="" disabled type="radio" value="dsc">
					<?php echo esc_html( __( 'DSC', 'ultimate-addons-cf7' ) ); ?>
				</label>
				<a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>
				<?php
				$order_by = ob_get_clean();
				echo apply_filters( 'uacf7_tag_generator_order_by_field', $order_by );
				?>
			</fieldset>

			<fieldset class="tag-generator-panel-product-id">
				<?php ob_start(); ?>
				<legend for="tag-generator-panel-product-id">
					<?php echo esc_attr( __( 'Product ID', 'ultimate-addons-cf7' ) ); ?>
				</legend>


				<textarea class="values" name="" id="tag-generator-panel-product-id" cols="30" rows="10" disabled></textarea>
				<br>
				One ID per line.
				<a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">
					(Pro)
				</a>

				<?php
				$product_id_html = ob_get_clean();
				/*
				 * Tag generator field after name attribute.
				 */
				echo apply_filters( 'uacf7_tag_generator_product_id_field', $product_id_html );
				?>
			</fieldset>


			<fieldset class="tag-generator-panel-product-category">
				<?php ob_start(); ?>

				<legend for="tag-generator-panel-product-category">
					<?php echo esc_attr( __( 'Product Category', 'ultimate-addons-cf7' ) ); ?>
				</legend>

				<div>
					<?php
					$taxonomies = get_terms( array(
						'taxonomy' => 'product_cat',
						'hide_empty' => false
					) );
					if ( $woo_activation == true ) :
						if ( ! empty( array_filter( $taxonomies ) ) ) :
							$output = '<select id="tag-generator-panel-product-category">';
							$output .= '<option value="">All</option>';
							foreach ( $taxonomies as $category ) {
								$output .= '<option value="">' . esc_html( $category->name ) . '</option>';
							}
							$output .= '</select> <a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>';

							echo $output;
						endif;
					else :
						$output = '<select id="tag-generator-panel-product-category">';
						$output .= '<option value="">All</option>';
						$output .= '</select> <a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>';
						echo $output;
						echo '<p style="color:red">Please install and activate WooCommerce plugin.</p>';
					endif;
					?>
				</div>

				<?php
				$product_dropdown_html = ob_get_clean();

				/*
				 * Tag generator field after name attribute.
				 */
				// echo $product_dropdown_html;
				echo apply_filters( 'uacf7_tag_generator_product_category_field', $product_dropdown_html );
				?>
			</fieldset>

			<fieldset class="tag-generator-panel-product-tag">
				<?php ob_start(); ?>

				<legend for="tag-generator-panel-product-tag">
					<?php echo esc_attr( __( 'Product tag', 'ultimate-addons-cf7' ) ); ?>
				</legend>

				<div>
					<?php
					$taxonomies = get_terms( array(
						'taxonomy' => 'product_tag',
						'hide_empty' => false
					) );
					if ( $woo_activation == true ) :
						if ( ! empty( array_filter( $taxonomies ) ) ) :
							$output = '<select data-tag-part="value" id="tag-generator-panel-product-tag">';
							$output .= '<option value="all">All</option>';
							foreach ( $taxonomies as $tag ) {
								$output .= '<option value="' . esc_attr( $tag->slug ) . '">' . esc_html( $tag->name ) . '</option>';
							}
							$output .= '</select> <a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>';

							echo $output;
						endif;
					else :
						$output = '<select id="tag-generator-panel-product-tag">';
						$output .= '<option value="">All</option>';
						$output .= '</select> <a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>';
						echo $output;
						echo '<p style="color:red">Please install and activate WooCommerce plugin.</p>';
					endif;
					?>
				</div>

				<?php
				$product_tag_html = ob_get_clean();

				/*
				 * Tag generator field after name attribute.
				 */
				echo apply_filters( 'uacf7_tag_generator_product_tag_field', $product_tag_html );
				?>
			</fieldset>

			<fieldset>
				<?php ob_start(); ?>
				<legend>
					<?php echo esc_html( __( 'Layout Style', 'ultimate-addons-cf7' ) ); ?>
				</legend>

				<label for="layoutDropdown"><input id="layoutDropdown" name="layout" class="option" disabled type="radio"
						value="dropdown"> Dropdown</label>

				<label for="layoutGrid"><input id="uacf7-select2" name="layout" class="option" type="radio" disabled
						value="select2"> Select 2</label>
				<label for="layoutGrid"><input id="layoutGrid" name="layout" class="option" type="radio" disabled value="grid">
					Grid</label>
				<a style="color:red" target="_blank" href="https://cf7addons.com/pricing/">(Pro)</a>

				<?php
				$select_layout_style = ob_get_clean();
				echo apply_filters( 'uacf7_tag_generator_product_layout_style_by_field', $select_layout_style );
				?>

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
new UACF7_PRODUCT_DROPDOWN();
