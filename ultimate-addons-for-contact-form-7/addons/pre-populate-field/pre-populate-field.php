<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
* Pre Populate Classs
*/
class UACF7_PRE_POPULATE {
    
    /*
    * Construct function
    */
    public function __construct() { 
        add_action( 'wp_enqueue_scripts', array($this, 'wp_enqueue_script' ) );    
        add_action( 'wp_ajax_uacf7_ajax_pre_populate_redirect', array( $this, 'uacf7_ajax_pre_populate_redirect' ) ); 
        add_action( 'wp_ajax_nopriv_uacf7_ajax_pre_populate_redirect', array( $this, 'uacf7_ajax_pre_populate_redirect' ) ); 
        add_filter( 'uacf7_post_meta_options', array($this, 'uacf7_post_meta_options_pre_populated'), 22, 2 ); 
        
    } 


    public function uacf7_post_meta_options_pre_populated( $value, $post_id){
        $list_forms = get_posts(array(
            'post_type'     => 'wpcf7_contact_form',
            'posts_per_page'   => -1
        ));
        $all_forms = array();
        foreach ($list_forms as $form) { 
            $all_forms[$form->ID] = $form->post_title; 
        }
        $pre_populated = apply_filters('uacf7_post_meta_options_pre_populated_pro', $data = array(
            'title'  => __( 'Pre-Populate Field', 'ultimate-addons-cf7' ),
            'icon'   => 'fa-solid fa-arrow-up-right-dots',
            'checked_field'   => 'pre_populate_enable',
            'fields' => array(
                'uacf7_pre_populated_heading' => array(
                    'id'    => 'uacf7_pre_populated_heading',
                    'type'  => 'heading', 
                    'label' => __( 'Pre-Populate Field Settings', 'ultimate-addons-cf7' ),
                    'subtitle' => sprintf(
                        __( 'Sends data from one form to another, after the first form submission. See Demo %1s.', 'ultimate-addons-cf7' ),
                         '<a href="https://cf7addons.com/preview/contact-form-7-pre-populate-fields/" target="_blank" rel="noopener">Example</a>'
                                  )
                      ),
                      'pre_populate_docs' => array(
                        'id'      => 'pre_populate_docs',
                        'type'    => 'notice',
                        'style'   => 'success',
                        'content' => sprintf( 
                            __( 'Confused? Check our Documentation on  %1s.', 'ultimate-addons-cf7' ),
                            '<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-pre-populate-fields/" target="_blank" rel="noopener">Pre-populate Field</a>'
                        )
                      ),
                'pre_populate_enable' => array(
                    'id'        => 'pre_populate_enable',
                    'type'      => 'switch',
                    'label'     => __( ' Enable Pre-Populate Field', 'ultimate-addons-cf7' ),
                    'label_on'  => __( 'Yes', 'ultimate-addons-cf7' ),
                    'label_off' => __( 'No', 'ultimate-addons-cf7' ),
                    'default'   => false, 
                ),
                'pre_populate_form_options_heading' => array(
                    'id'        => 'pre_populate_form_options_heading',
                    'type'      => 'heading',
                    'label'     => __( 'Pre Populate Option ', 'ultimate-addons-cf7' ),
                ),
                'pre_populate_form' => array(
                    'id'        => 'pre_populate_form',
                    'type'      => 'select',
                    'label'     => __( ' Select Other Form', 'ultimate-addons-cf7' ),
                    'subtitle'     => __( 'The data will be sent to this form.', 'ultimate-addons-cf7' ),
                    'options'     => $all_forms, 
                ),
                'data_redirect_url' => array(
                    'id'        => 'data_redirect_url',
                    'type'      => 'text',
                    'label'     => __( ' Redirect URL ', 'ultimate-addons-cf7' ),
                    'subtitle'     => __( 'Insert the Page URL of the Other Form.', 'ultimate-addons-cf7' ),
                    'placeholder'     => __( ' Redirect URL ', 'ultimate-addons-cf7' ), 
                ),

              'pre_populate_passing_field' => array(
                'id' => 'pre_populate_passing_field',
                'type' => 'repeater',
                'label' => 'Select Pre-Populate Field',
                'subtitle'     => __( 'The data inserted on these fields will be forwarded to the other form.', 'ultimate-addons-cf7' ),
                'class' => 'tf-field-class',
                'fields' => array(
                    'field_name' => array(
                        'id' => 'field_name',
                        'type' => 'select',
                        'options'  => 'uacf7',
                        'query_args' => array(
                            'post_id'      => $post_id,  
                            'exclude'      => ['submit'], 
                        ), 
                     )
                 ),
            )
    
            ),
                
    
        ), $post_id);
    
        $value['pre_populated'] = $pre_populated; 
        return $value;
    }

    /*
    * Enqueue script Forntend
    */
    
    public function wp_enqueue_script() {
		wp_enqueue_script( 'pre-populate-script', UACF7_ADDONS . '/pre-populate-field/assets/js/pre-populate.js', array('jquery'), null, true ); 
        wp_localize_script( 'pre-populate-script', 'pre_populate_url',
            array( 
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce('uacf7-pre-populate')
                )
        );
    }
 
 
    /*
    * Product Pre-populate redirect with value after submiting form by ajax
    */
    
    public function uacf7_ajax_pre_populate_redirect() { 
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
		}
        
        if ( !wp_verify_nonce($_POST['ajax_nonce'], 'uacf7-pre-populate')) {
            exit(esc_html__("Security error", 'ultimate-addons-cf7'));
        }

        $form_id = $_POST['form_id']; 
        $pre_populate = uacf7_get_form_option( $form_id, 'pre_populated' );
        $pre_populate_enable = isset($pre_populate['pre_populate_enable']) ? $pre_populate['pre_populate_enable'] : false;
        if($pre_populate_enable == true){
            $data_redirect_url = isset($pre_populate['data_redirect_url']) ? $pre_populate['data_redirect_url'] : '#';
            $pre_populate_passing_field = isset($pre_populate['pre_populate_passing_field']) ? $pre_populate['pre_populate_passing_field'] : '';
            $pre_populate_form = isset($pre_populate['pre_populate_form']) ? $pre_populate['pre_populate_form'] : '';
            $field_name = array();
            if(is_array($pre_populate_passing_field)){
                foreach ($pre_populate_passing_field as $value) {
                    $field_name[] = $value['field_name'];
                }
            }
            $data = [
                'form_id' => $form_id,
                'pre_populate_enable' => $pre_populate_enable,
                'data_redirect_url' => $data_redirect_url,
                'pre_populate_passing_field' => array_values($field_name),
                'pre_populate_form' => $pre_populate_form,
            ];
            
            echo wp_send_json($data);
        }else{
            echo false;
        }  
        wp_die();
    }
   
}
new UACF7_PRE_POPULATE();
