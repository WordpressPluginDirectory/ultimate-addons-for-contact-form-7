<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_PROMO_NOTICE {

    // private $api_url = 'http://uacf7-api.test/';
    private $api_url = 'https://api.themefic.com/';
    private $args = array();
    private $responsed = false; 
    private $uacf7_promo_option = false; 
    private $error_message = ''; 

    private $months = ['January', 'June', 'November', 'December']; 
    private $plugins_existes = ['ins', 'tf', 'beaf', 'ebef'];

    public function __construct() {

        if(in_array(date('F'), $this->months) && !class_exists('Ultimate_Addons_CF7_PRO')){ 
            add_filter('cron_schedules', array($this, 'uacf7_custom_cron_interval'));
        
            if (!wp_next_scheduled('uacf7_promo__schudle')) {
                wp_schedule_event(time(), 'every_day', 'uacf7_promo__schudle');
            }
            
            add_action('uacf7_promo__schudle', array($this, 'uacf7_promo__schudle_callback'));
             
            if(get_option( 'uacf7_promo__schudle_option' )){
                $this->uacf7_promo_option = get_option( 'uacf7_promo__schudle_option' );
            }

            $tf_existes = get_option( 'tf_promo_notice_exists' );
             
            // Admin Notice 
            if( ! in_array($tf_existes, $this->plugins_existes) && is_array($this->uacf7_promo_option) && strtotime($this->uacf7_promo_option['end_date']) > time() && strtotime($this->uacf7_promo_option['start_date']) < time()){
                add_action( 'admin_notices', array( $this, 'tf_black_friday_2023_admin_notice' ) );
                add_action( 'wp_ajax_tf_black_friday_notice_dismiss_callback', array($this, 'tf_black_friday_notice_dismiss_callback') );
            }
            
            // side Notice 
            if(is_array($this->uacf7_promo_option) && strtotime($this->uacf7_promo_option['end_date']) > time() && strtotime($this->uacf7_promo_option['start_date']) < time()){ 
                add_action( 'wpcf7_admin_misc_pub_section', array( $this, 'uacf7_black_friday_2022_callback' ) );
                add_action( 'wp_ajax_uacf7_black_friday_notice_cf7_dismiss_callback', array($this, 'uacf7_black_friday_notice_cf7_dismiss_callback') ); 
            } 


            register_deactivation_hook( UACF7_PATH . 'ultimate-addons-for-contact-form-7.php', array($this, 'uacf7_promo_notice_deactivation_hook') );
        }

        
       
    }

    public function uacf7_get_api_response(){
        $query_params = array(
            'plugin' => 'uacf7', 
        );
        $response = wp_remote_post($this->api_url, array(
            'body'    => json_encode($query_params),
            'headers' => array('Content-Type' => 'application/json'),
        )); 
        if (is_wp_error($response)) {
            // Handle API request error
            $this->responsed = false;
            $this->error_message = esc_html($response->get_error_message());
 
        } else {
            // API request successful, handle the response content
            $data = wp_remote_retrieve_body($response);
           
            $this->responsed = json_decode($data, true); 

            $uacf7_promo__schudle_option = get_option( 'uacf7_promo__schudle_option' ); 
            if(isset($ins_promo__schudle_option['notice_name']) && $uacf7_promo__schudle_option['notice_name'] != $this->responsed['notice_name']){ 
                // Unset the cookie variable in the current script
                update_option( 'tf_dismiss_admin_notice', 1);
                update_option( 'uacf7_dismiss_post_notice', 1); 
            }
            update_option( 'uacf7_promo__schudle_option', $this->responsed);
            
        } 
    }

    // Define the custom interval
    public function uacf7_custom_cron_interval($schedules) {
        $schedules['every_day'] = array(
            'interval' => 86400, // Every 24 hours
            // 'interval' => 5, // Every 24 hours
            'display' => __('Every 24 hours')
        );
        return $schedules;
    }

    public function uacf7_promo__schudle_callback() {  

        $this->uacf7_get_api_response();

    }
 

    /**
     * Black Friday Deals 2023
     */
    
    public function tf_black_friday_2023_admin_notice(){ 
        
        $image_url = isset($this->uacf7_promo_option['dasboard_url']) ? esc_url($this->uacf7_promo_option['dasboard_url']) : '';
        $deal_link = isset($this->uacf7_promo_option['promo_url']) ? esc_url($this->uacf7_promo_option['promo_url']) : ''; 

        $tf_dismiss_admin_notice = get_option( 'tf_dismiss_admin_notice' );
        $get_current_screen = get_current_screen();  
        if(($tf_dismiss_admin_notice == 1  || time() >  $tf_dismiss_admin_notice ) && $get_current_screen->base == 'dashboard'   ){ 
          
         // if very fist time then set the dismiss for our other plugins
           update_option( 'tf_promo_notice_exists', 'uacf7' );
           
           ?>
            <style> 
                .tf_black_friday_20222_admin_notice a:focus {
                    box-shadow: none;
                } 
                .tf_black_friday_20222_admin_notice {
                    padding: 7px;
                    position: relative;
                    z-index: 10;
                    max-width: 825px;
                } 
                .tf_black_friday_20222_admin_notice button:before {
                    color: #fff !important;
                }
                .tf_black_friday_20222_admin_notice button:hover::before {
                    color: #d63638 !important;
                }
            </style>
            <div class="notice notice-success tf_black_friday_20222_admin_notice"> 
                <a href="<?php echo esc_attr( $deal_link ); ?>" style="display: block; line-height: 0;" target="_blank" >
                    <img  style="width: 100%;" src="<?php echo esc_attr($image_url) ?>" alt="">
                </a> 
                <?php if( isset($this->uacf7_promo_option['dasboard_dismiss']) && $this->uacf7_promo_option['dasboard_dismiss'] == true): ?>
                <button type="button" class="notice-dismiss tf_black_friday_notice_dismiss"><span class="screen-reader-text"><?php echo __('Dismiss this notice.', 'ultimate-addons-cf7' ) ?></span></button>
                <?php  endif; ?>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_black_friday_notice_dismiss', function( event ) {
                        jQuery('.tf_black_friday_20222_admin_notice').css('display', 'none')
                        data = {
                            action : 'tf_black_friday_notice_dismiss_callback',
                        };

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: data,
                            success: function (data) { ;
                            },
                            error: function (data) { 
                            }
                        });
                    });
                });
            </script>
        
        <?php 
        }
        
    } 


    public function tf_black_friday_notice_dismiss_callback() {  

        $uacf7_promo_option = get_option( 'uacf7_promo__schudle_option' );
        $restart = isset($uacf7_promo_option['dasboard_restart']) && $uacf7_promo_option['dasboard_restart'] != false ? $uacf7_promo_option['dasboard_restart'] : false; 
        if($restart == false){
            update_option( 'tf_dismiss_admin_notice', strtotime($uacf7_promo_option['end_date']) ); 
        }else{
            update_option( 'tf_dismiss_admin_notice', time() + (86400 * $restart) );  
        } 
		wp_die();
	}


    public function uacf7_black_friday_2022_callback(){
        $image_url = isset($this->uacf7_promo_option['side_url']) ? esc_url($this->uacf7_promo_option['side_url']) : '';
        $deal_link = isset($this->uacf7_promo_option['promo_url']) ? esc_url($this->uacf7_promo_option['promo_url']) : ''; 
        $uacf7_dismiss_post_notice = get_option( 'uacf7_dismiss_post_notice' );
        ?> 
         <?php if($uacf7_dismiss_post_notice == 1  || time() >  $uacf7_dismiss_post_notice ): ?>
            <style> 
                .back_friday_2022_preview a:focus {
                    box-shadow: none;
                } 
                .back_friday_2022_preview a {
                    display: inline-block;
                }
                #uacf7_black_friday_docs .inside {
                    padding: 0;
                    margin-top: 0;
                }
                #uacf7_black_friday_docs .postbox-header {
                    display: none;
                    visibility: hidden;
                }
                .back_friday_2022_preview {
                    position: relative;
                }
                .tf_black_friday_cf7_notice_dismiss {
                    position: ;
                    z-index: 1;
                }
            
            </style>
            
           
            <div class="back_friday_2022_preview" style="text-align: center; overflow: hidden; margin: 10px;">
                <a href="<?php echo esc_attr($deal_link); ?>" target="_blank" >
                    <img  style="width: 100%;" src="<?php echo esc_attr($image_url); ?>" alt="">
                </a>  
                <?php if( isset($this->uacf7_promo_option['side_dismiss']) && $this->uacf7_promo_option['side_dismiss'] == true): ?>
                    <button type="button" class="notice-dismiss tf_black_friday_cf7_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                <?php  endif; ?>
                
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_black_friday_cf7_notice_dismiss', function( event ) { 
                        jQuery('.back_friday_2022_preview').css('display', 'none')
                        data = {
                            action : 'uacf7_black_friday_notice_cf7_dismiss_callback', 
                        };

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: data,
                            success: function (data) { ;
                            },
                            error: function (data) { 
                            }
                        });
                    });
                });
            </script>
            <?php endif; ?>
        <?php
	}

    public  function uacf7_black_friday_notice_cf7_dismiss_callback() {   
        $uacf7_promo_option = get_option( 'uacf7_promo__schudle_option' );
        $start_date = isset($uacf7_promo_option['start_date']) ? strtotime($uacf7_promo_option['start_date']) : time();
        $restart = isset($uacf7_promo_option['side_restart']) && $uacf7_promo_option['side_restart'] != false ? $uacf7_promo_option['side_restart'] : 5;
        update_option( 'uacf7_dismiss_post_notice', time() + (86400 * $restart) );  
        wp_die();
    }

     // Deactivation Hook
     public function uacf7_promo_notice_deactivation_hook() {
        wp_clear_scheduled_hook('uacf7_promo__schudle'); 

        delete_option('uacf7_promo__schudle_option');
        delete_option('tf_promo_notice_exists');
    }
 
}

new UACF7_PROMO_NOTICE();
