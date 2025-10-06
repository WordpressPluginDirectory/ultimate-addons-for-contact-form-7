<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UACF7_select2' ) ) {
	class UACF7_select2 extends UACF7_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '',  $section_key = ''  ) {
			parent::__construct( $field, $value, $settings_id, $parent_field , $section_key );
		}

		public function render() {

			if(empty($this->field['options']) && empty($this->field['options_callback'])) {
				return;
			}

			$args = wp_parse_args( $this->field, array(
				'placeholder' => '',
				'multiple'    => false,
			) );

			if(isset($this->field['options_callback']) && is_callable($this->field['options_callback'])) {
				$this->field['options'] = call_user_func($this->field['options_callback']);
			}

			$placeholder = ( ! empty( $args['placeholder'] ) ) ? $args['placeholder'] : '';
			$multiple    = ( ! empty( $args['multiple'] ) ) ? 'multiple' : '';

			if(!empty($args['query_args']) && $args['options'] == 'posts'){
				$posts = get_posts($args['query_args']);
				$args['options'] = array();
				foreach($posts as $post){
					$args['options'][$post->ID] = (empty($post->post_title)) ? 'No title ('.$post->ID.')' : $post->post_title;
				}
			}

			if(!empty($args['query_args']) && $args['options'] == 'terms'){
				$terms = get_terms($args['query_args']);
				$args['options'] = array();
				foreach($terms as $term){
					$args['options'][$term->term_id] = $term->name;
				}
			}

			if ( ! empty( $args['query_args'] ) && $args['options'] == 'uacf7' ) { 
				$post_id  = isset( $args['query_args']['post_id'] ) ? (int) $args['query_args']['post_id'] : 0; 
				$args['options'] = [];

				if ( $post_id > 0 ) {
					$specific = isset( $args['query_args']['specific'] ) ? $args['query_args']['specific'] : '';  
					$ContactForm = WPCF7_ContactForm::get_instance( $post_id ); 

					$tags = ( $specific != '' )
						? $ContactForm->scan_form_tags( array( 'basetype'=> $specific ) )
						: $ContactForm->scan_form_tags();

					$exclude = isset( $args['query_args']['exclude'] ) ? $args['query_args']['exclude'] : array();

					foreach ( $tags as $tag ) { 
						if ( $tag['type'] == '' || in_array( $tag['basetype'], $exclude ) ) continue; 

						if ( $tag['type'] == 'checkbox' ) {   
							$tag_name = ( is_array( $tag['options'] ) && !in_array( 'exclusive', $tag['options'] ) )
								? $tag['name'].'[]'
								: $tag['name'];
						} elseif ( $tag['type'] == 'select' ) {    
							$tag_name = ( is_array( $tag['options'] ) && in_array( 'multiple', $tag['options'] ) )
								? $tag['name'].'[]'
								: $tag['name'];
						} else { 
							$tag_name = $tag['name'];
						}

						if ( $tag['name'] == '' && $tag['type'] == 'uarepeater' ) {
							$attrs = explode( ' ', $tag['attr'] );  
							if ( $attrs == '' ) {
								$attrs = $tag['options'];
							} 
							$args['options'][ $attrs[0] ] = esc_html( $attrs[0] );  
						} else {
							$args['options'][ $tag_name ] = esc_html( $tag['name'] ); 
						}
					}
				}
			}

			if ( ! empty( $args['query_args'] ) && $args['options'] == 'mailchimp_tags' ) {
				$post_id  = isset( $args['query_args']['post_id'] ) ? (int) $args['query_args']['post_id'] : 0;
				$args['options'] = [];
				
				if ( $post_id > 0 ) {
					
					$addon_option = get_option( 'uacf7_settings' );
					$uacf7_existing_plugin_status = get_option( 'uacf7_existing_plugin_status' );

					if ( apply_filters( 'uacf7_checked_license_status', '' ) == false || $uacf7_existing_plugin_status != 'done' ) {
						return;
					}
					//Addon - Addon mailchimp pro is enabled
					if ( isset( $addon_option['uacf7_enable_mailchimp_pro'] ) && $addon_option['uacf7_enable_mailchimp_pro'] != true ) {
						return;
					}
					// Get API key
					$api_key = uacf7_settings( 'uacf7_mailchimp_api_key' ); // adjust option name
					// Get selected audience for this form
					$mailchimp = uacf7_get_form_option( $post_id, 'mailchimp' );
					$audience_id = isset( $mailchimp['uacf7_mailchimp_audience'] ) ? $mailchimp['uacf7_mailchimp_audience'] : '';

					if ( $api_key && $audience_id ) {
						$path     = "lists/$audience_id/tag-search?count=100";
						$response = $this->set_config( $api_key, $path );
						$response = json_decode( $response, true );

						if ( ! empty( $response['tags'] ) && is_array( $response['tags'] ) ) {
							foreach ( $response['tags'] as $tag ) {
								$args['options'][ $tag['name'] ] = $tag['name'];
							}
						}
					}
				}
			}

			$field_name           = !empty($this->field['multiple']) ? $this->field_name() . '[]' : $this->field_name();
			$tf_select2_unique_id = str_replace( array("[","]"),"_",esc_attr( $this->field_name() ) );
			$parent_class         = ( ! empty( $this->parent_field ) ) ? 'tf-select2-parent' : 'tf-select2';
			$parent_class         = ( isset( $this->field['select2'] ) ) ? 'tf-select2' : $parent_class ;

			echo '<select name="' . $field_name . '" id="' . $tf_select2_unique_id . '" class=" tf-select-two '.$parent_class.' " data-placeholder="' . esc_attr( $placeholder ) . '" ' . $multiple . ' '. $this->field_attributes() .'>';
			foreach ( $args['options'] as $key => $value ) {
				$selected = '';

				if ( ! empty( $this->field['multiple'] ) ) {
					if ( is_array( $this->value ) && in_array( $key, $this->value ) ) {
						$selected = 'selected';
					}
				} else {
					$selected = selected( $this->value, $key, false );
				}

				echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $value ) . '</option>';
			}

			echo '</select>';
		}

		//sanitize
		public function sanitize() {
			$value = $this->value;
			if ( ! empty( $this->field['multiple'] ) && is_array( $this->value ) ) {
				$value = array_map( 'sanitize_text_field', $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			return $value;
		}

		private function set_config( $api_key = '', $path = '' ) {

			$server_prefix = explode( "-", $api_key );

			if ( ! isset( $server_prefix[1] ) ) {
				return;
			}
			$server_prefix = $server_prefix[1];

			$url = "https://$server_prefix.api.mailchimp.com/3.0/$path";

			$curl = curl_init( $url );
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

			$headers = array(
				"Authorization: Bearer $api_key"
			);
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
			//for debug only!
			curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

			$resp = curl_exec( $curl );
			curl_close( $curl );

			return $resp;
		}

	}
}