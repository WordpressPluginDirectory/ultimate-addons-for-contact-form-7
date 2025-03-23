<?php

use phpDocumentor\Reflection\Types\This;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_Redirection {
    
    /*
    * Construct function
    */
    public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_redirect_script' ) );  
		// add_action( 'wpcf7_after_save', array( $this, 'uacf7_save_meta' ) );
		add_action( 'wpcf7_submit', array( $this, 'uacf7_non_ajax_redirection' ) );
		add_filter( 'uacf7_post_meta_options', array( $this, 'uacf7_post_meta_options_redirection' ), 10, 2 );

		// add_action('admin_notices', array($this, 'uacf7_redirection_migration_notice'));
		// add_action('admin_init', array($this, 'uacf7_migrate_redirection_handler'));
		// add_action('admin_notices', array($this, 'uacf7_redirection_migration_success_notice'));
		// add_action('admin_init', array($this, 'uacf7_handle_dismiss_notice'));
    }
    
    public function enqueue_redirect_script() {
		
        wp_enqueue_script( 'uacf7-redirect-script', UACF7_URL . 'addons/redirection/js/redirect.js', array('jquery'), null, true );
		wp_localize_script( 'uacf7-redirect-script', 'uacf7_redirect_object', $this->get_forms() );
        wp_localize_script( 'uacf7-redirect-script', 'uacf7_redirect_enable', $this->uacf7_redirect_enable() );
        
		if ( isset( $this->enqueue_new_tab_script ) && $this->enqueue_new_tab_script ) {
			wp_add_inline_script( 'wpcf7-redirect-script', 'window.open("' . $this->redirect_url . '");' );
		}
    }
 
	
    public function uacf7_post_meta_options_redirection($value, $post_id) {
		
		$redirection = apply_filters('uacf7_post_meta_options_redirection_pro', $data = array(
			'title'  => __( 'Redirection', 'ultimate-addons-cf7' ),
			'icon'   => 'fa-solid fa-diamond-turn-right',
            'checked_field'   => 'uacf7_redirect_enable',
			'fields' => array( 
				'redirection_heading' => array(
					'id'    => 'redirection_heading',
					'type'  => 'heading', 
					'label' => __( 'Redirection Settings', 'ultimate-addons-cf7' ),
					'subtitle' => sprintf(
                        __( 'Redirect users to a Thank You or external page based on form submission, with an option to open in a new tab. See Demo %1s.', 'ultimate-addons-cf7' ),
                         '<a href="https://cf7addons.com/preview/redirection-for-contact-form-7/" target="_blank">Example</a>'
                    )
				),
				'redirection_docs' => array(
					'id'      => 'redirection_docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => sprintf( 
                        __( 'Confused? Check our Documentation on  %1s, %2s and %3s .', 'ultimate-addons-cf7' ),
                        '<a href="https://themefic.com/docs/uacf7/free-addons/redirection-for-contact-form-7/" target="_blank">Redirect to a Page or External URL</a>',
                        '<a href="https://themefic.com/docs/uacf7/pro-addons/conditional-redirect-for-contact-form-7/" target="_blank">Conditional Redirect</a>',
                        '<a href="https://themefic.com/docs/uacf7/pro-addons/contact-form-7-whatsapp-integration-and-tag-support/" target="_blank">Tag Support</a>'
                    )
				),
				'uacf7_redirect_enable' => array(
					'id'        => 'uacf7_redirect_enable',
					'type'      => 'switch',
					'label'     => __( ' Enable Redirection', 'ultimate-addons-cf7' ),
					'label_on'  => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default'   => false
				),
				'uacf7_redirect_form_options_heading' => array(
                    'id'        => 'uacf7_redirect_form_options_heading',
                    'type'      => 'heading',
                    'label'     => __( 'Redirection Option ', 'ultimate-addons-cf7' ),
                ),
				'uacf7_redirect_to_type' => array(
					'id'        => 'uacf7_redirect_to_type',
					'type'      => 'radio',
					'label'     => __( 'Redirect to', 'ultimate-addons-cf7' ),
					'options' => array(
						'to_page' => 'Redirect to Internal Page ',
						'to_url' => 'Redirect to External URL ',
					 ),
					 'default' => 'to_page',
					 'inline' => true,
					 'dependency' => array( 'uacf7_redirect_type', '==', false ),
				),
				'page_id' => array(
					'id'        => 'page_id',
					'type'      => 'select',
					'label'     => __( 'Select the Redirection Page ', 'ultimate-addons-cf7' ),  
					'options'     => 'posts', 
					'query_args'  => array(
						'post_type'      => 'page',
						'posts_per_page' => - 1,
					),
					'multiple' => true,
					'dependency' => array(array( 'uacf7_redirect_to_type', '==', 'to_page' ), array( 'uacf7_redirect_type', '==', false )),
				),
				'external_url' => array(
					'id'        => 'external_url',
					'type'      => 'text',
					'label'     => __( 'Insert Any URL', 'ultimate-addons-cf7' ),   
					'dependency' => array(array( 'uacf7_redirect_to_type', '==', 'to_url' ), array( 'uacf7_redirect_type', '==', false )),
				),
				'uacf7_redirect_type' => array(
					'id'        => 'uacf7_redirect_type',
					'type'      => 'switch',
					'label'     => __( 'Conditional Redirect', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Redirect users to different webpages based on specific conditions. For example, if Condition A is met, the user is redirected to abc.com, while Condition B leads the user to xyz.com.', 'ultimate-addons-cf7' ),
					'label_on'  => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default'   => false,
					'is_pro' => true,
				),
				'conditional_redirect' => array(
					'id' => 'conditional_redirect',
					'type' => 'repeater',
					'label' => 'Conditional Redirection Settings',
					'subtitle' => __( "The process works as follows: You select a field and specify a value. If the user's input matches the value you set for that field, they will then be redirected to the specified URL.", 'ultimate-addons-cf7' ),
					'class' => 'tf-field-class',
					'dependency' => array( 'uacf7_redirect_type', '==', true ),
					'fields' => array(
						'uacf7_cr_tn' => array(
							'id' => 'uacf7_cr_tn',
							'label' => 'Select Form Field',
							'subtitle' => 'This determines the basis for setting the condition.',
							'type' => 'select', 
							'field_width' => 50,
						 ),
						array(
							'id' => 'uacf7_cr_field_val',
							'label' => 'Conditional Value',
							'type' => 'text',
							'subtitle' => 'Input the specific value that will trigger the condition.',
							'placeholder' => 'value', 
							'field_width' => 50,
						 ),
						array(
							'id' => 'uacf7_cr_redirect_to_url',
							'label' => 'Redirect to',
							'type' => 'text',
							'subtitle' => 'The URL to which the user will be redirected upon meeting the condition.',
							'placeholder' => 'Redirection URL', 
							'field_width' => 100,
						 ),
					 ),
				),
				'target' => array(
					'id'        => 'target',
					'type'      => 'switch',
					'label'     => __( 'Open Page in a New Tab', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Enable this to open the redirection page in a new tab.', 'ultimate-addons-cf7' ),
					'label_on'  => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default'   => false,
					'field_width' => 50,
				),
				'uacf7_redirect_tag_support' => array(
					'id'        => 'uacf7_redirect_tag_support',
					'type'      => 'switch',
					'label'     => __( 'Whatsapp or Tags Support', 'ultimate-addons-cf7' ),
					'subtitle' => __( 'Add tags on URL / Pass data to Whatsapp.', 'ultimate-addons-cf7' ),
					'label_on'  => __( 'Yes', 'ultimate-addons-cf7' ),
					'label_off' => __( 'No', 'ultimate-addons-cf7' ),
					'default'   => false,
					'is_pro' => true,
					'field_width' => 50,
				),
				
			),
		), $post_id);
		$value['redirection'] = $redirection;  
		return $value;
	}
   
 

    public function get_forms() {
		$args  = array(
			'post_type'        => 'wpcf7_contact_form',
			'posts_per_page'   => -1,
		);
		$query = new WP_Query( $args );

		$forms = array();

		if ( $query->have_posts() ) :

			
			$fields = $this->fields(); 

			while ( $query->have_posts() ) :
				$query->the_post();

				$post_id = get_the_ID(); 
				$post_meta = uacf7_get_form_option($post_id, 'redirection');
				if($post_meta != false){
					foreach ( $fields as $field ) {
						// $forms[ $post_id ][ $field['name'] ] = get_post_meta( $post_id, 'uacf7_redirect_' . $field['name'], true );
						$forms[ $post_id ][ $field['name'] ] = $post_meta[$field['name']];
					}
	
					$forms[ $post_id ]['thankyou_page_url'] = $forms[ $post_id ]['page_id'] ? get_permalink( $forms[ $post_id ]['page_id'] ) : '';
				}
				
			endwhile;
			wp_reset_postdata();
		endif;

		return $forms;
	}
    
    public function uacf7_get_options( $post_id ) {
		$fields = $this->fields();
		$post_meta = uacf7_get_form_option($post_id, 'redirection');
		foreach ( $fields as $field ) {
			$values[ $field['name'] ] = $post_meta[$field['name']];
		}
		return $values;
	}
    
 
    
    public function uacf7_non_ajax_redirection( $contact_form ) {
		$this->fields = $this->uacf7_get_options( $contact_form->id() );

		if ( isset( $this->fields ) && ! WPCF7_Submission::is_restful() ) {
			$submission = WPCF7_Submission::get_instance();

			if ( $submission->get_status() === 'mail_sent' ) {

				if ( 'to_url' === $this->fields['uacf7_redirect_to_type'] && $this->fields['external_url'] ) {
					$this->redirect_url = $this->fields['external_url'];
				}
				if( 'to_page' === $this->fields['uacf7_redirect_to_type'] && $this->fields['page_id'] ){
					$this->redirect_url = get_permalink( $this->fields['page_id'] );
				}

				// Open link in a new tab
				if ( isset( $this->redirect_url ) && $this->redirect_url ) {
					if ( 'on' === $this->fields['open_in_new_tab'] ) {
						$this->enqueue_new_tab_script = true;
					} else {
						wp_redirect( $this->redirect_url );
						exit;
					}
				}
			}
		}
	}
    

    /*
    * Fields array
    */
    public function fields() {
        $fields = array(
            array(
                'name'  => 'uacf7_redirect_to_type',
                'type'  => 'radio',
            ),
			array(
                'name'  => 'page_id',
                'type'  => 'number',
            ),
            array(
                'name'  => 'external_url',
                'type'  => 'url',
            ),
            array(
                'name'  => 'target',
                'type'  => 'checkbox',
            ),
        );
        return $fields;
    }
 
    /*
    Enable conditional redirect
    */
    public function uacf7_redirect_enable() {
    	$args  = array(
    		'post_type'        => 'wpcf7_contact_form',
    		'posts_per_page'   => -1,
    	);
    	$query = new WP_Query( $args );
    
    	$forms = array();
    
    	if ( $query->have_posts() ) :
    
    		while ( $query->have_posts() ) :
    			$query->the_post();
    
                $post_id = get_the_ID();
                
                // $uacf7_redirect = get_post_meta( get_the_ID(), 'uacf7_redirect_enable', true );
				$post_meta = uacf7_get_form_option(get_the_ID(), 'redirection');
				// beaf_print_r($post_meta);
				if($post_meta != false){
					$uacf7_redirect = $post_meta['uacf7_redirect_enable']; 
					
					if( !empty($uacf7_redirect) && $uacf7_redirect == true ) {
						
						$forms[ $post_id ] = $uacf7_redirect;
					
					}
				} 
    		endwhile;
    		wp_reset_postdata();
    	endif;
		// beaf_print_r($forms);
    	return $forms;
    }
}
new UACF7_Redirection();


