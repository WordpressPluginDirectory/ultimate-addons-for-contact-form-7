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

    private $months = [
        'January',  
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    private $plugins_existes = ['ins', 'tf', 'beaf', 'ebef'];

    public function __construct() {

        if(in_array(date('F'), $this->months) && !class_exists('Ultimate_Addons_CF7_PRO')){  

            $uacf7_promo__schudle_start_from = !empty(get_option( 'uacf7_promo__schudle_start_from' )) ? get_option( 'uacf7_promo__schudle_start_from' ) : 0;

            if($uacf7_promo__schudle_start_from == 0){
                // delete option
                delete_option('uacf7_promo__schudle_option');

            }elseif($uacf7_promo__schudle_start_from  != 0 && $uacf7_promo__schudle_start_from > time()){
                return;
            }  
            add_filter('cron_schedules', array($this, 'uacf7_custom_cron_interval'));
        
            if (!wp_next_scheduled('uacf7_promo__schudle')) {
                wp_schedule_event(time(), 'every_day', 'uacf7_promo__schudle');
            }
            
            add_action('uacf7_promo__schudle', array($this, 'uacf7_promo__schudle_callback'));
             
            if(get_option( 'uacf7_promo__schudle_option' )){
                $this->uacf7_promo_option = get_option( 'uacf7_promo__schudle_option' );
            }

            $tf_existes = get_option( 'tf_promo_notice_exists' );
            $dashboard_banner = isset($this->uacf7_promo_option['dashboard_banner']) ? $this->uacf7_promo_option['dashboard_banner'] : '';
            // Admin Notice  
            if( ! in_array($tf_existes, $this->plugins_existes) && is_array($dashboard_banner) && strtotime($dashboard_banner['end_date']) > time() && strtotime($dashboard_banner['start_date']) < time() && $dashboard_banner['enable_status'] == true){
            
                add_action( 'admin_notices', array( $this, 'tf_promo_dashboard_admin_notice' ) );
                add_action( 'wp_ajax_tf_black_friday_notice_dismiss_callback', array($this, 'tf_black_friday_notice_dismiss_callback') );
            }
            
            // side Notice 
            $service_banner = isset($this->uacf7_promo_option['service_banner']) ? $this->uacf7_promo_option['service_banner'] : array();
            $promo_banner = isset($this->uacf7_promo_option['promo_banner']) ? $this->uacf7_promo_option['promo_banner'] : array();

            $current_day = date('l'); 
            if(isset($service_banner['enable_status']) && $service_banner['enable_status'] == true && in_array($current_day, $service_banner['display_days'])){ 
             
                $start_date = isset($service_banner['start_date']) ? $service_banner['start_date'] : '';
                $end_date = isset($service_banner['end_date']) ? $service_banner['end_date'] : '';
                $enable_side = isset($service_banner['enable_status']) ? $service_banner['enable_status'] : false;
            }else{  
                $start_date = isset($promo_banner['start_date']) ? $promo_banner['start_date'] : '';
                $end_date = isset($promo_banner['end_date']) ? $promo_banner['end_date'] : '';
                $enable_side = isset($promo_banner['enable_status']) ? $promo_banner['enable_status'] : false;
            } 
            if(is_array($this->uacf7_promo_option) && strtotime($end_date) > time() && strtotime($start_date) < time()  && $enable_side == true){ 
                
                add_action( 'wpcf7_admin_misc_pub_section', array( $this, 'uacf7_promo_side_notice_callback' ) );
                add_action( 'wp_ajax_uacf7_promo_side_notice_cf7_dismiss_callback', array($this, 'uacf7_promo_side_notice_cf7_dismiss_callback') ); 
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
            if(isset($uacf7_promo__schudle_option['notice_name']) && $uacf7_promo__schudle_option['notice_name'] != $this->responsed['notice_name']){ 
                // Unset the cookie variable in the current script
                update_option( 'tf_dismiss_admin_notice', 1);
                update_option( 'uacf7_dismiss_post_notice', 1); 
                update_option( 'uacf7_promo__schudle_start_from', time() + 43200);
            }elseif(empty($uacf7_promo__schudle_option)){
                update_option( 'uacf7_promo__schudle_start_from', time() + 43200);
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
    
    public function tf_promo_dashboard_admin_notice(){ 
        
        $dashboard_banner = isset($this->uacf7_promo_option['dashboard_banner']) ? $this->uacf7_promo_option['dashboard_banner'] : '';
        $image_url = isset($dashboard_banner['banner_url']) ? esc_url($dashboard_banner['banner_url']) : '';
        $deal_link = isset($dashboard_banner['redirect_url']) ? esc_url($dashboard_banner['redirect_url']) : ''; 

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
                <?php if( isset($dashboard_banner['dismiss_status']) && $dashboard_banner['dismiss_status'] == true): ?>
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
	$restart = isset($uacf7_promo_option['dashboard_banner']['restart']) && $uacf7_promo_option['dashboard_banner']['restart'] != false ? $uacf7_promo_option['dashboard_banner']['restart'] : false;
        if($restart == false){
            update_option( 'tf_dismiss_admin_notice', strtotime($uacf7_promo_option['end_date']) ); 
        }else{
            update_option( 'tf_dismiss_admin_notice', time() + (86400 * $restart) );  
        } 
		wp_die();
	}




    public function uacf7_promo_side_notice_callback(){
        $service_banner = isset($this->uacf7_promo_option['service_banner']) ? $this->uacf7_promo_option['service_banner'] : array();
        $promo_banner = isset($this->uacf7_promo_option['promo_banner']) ? $this->uacf7_promo_option['promo_banner'] : array();
        

        $current_day = date('l'); 
        if( isset($service_banner['enable_status']) && $service_banner['enable_status'] == true && in_array($current_day, $service_banner['display_days'])){ 
           
            $image_url = esc_url($service_banner['banner_url']);
            $deal_link = esc_url($service_banner['redirect_url']);  
            $dismiss_status = $service_banner['dismiss_status'];
        }else{
            $image_url = esc_url($promo_banner['banner_url']);
            $deal_link = esc_url($promo_banner['redirect_url']); 
            $dismiss_status = $promo_banner['dismiss_status'];  
        }  
        $uacf7_dismiss_post_notice = get_option( 'uacf7_dismiss_post_notice' );
        ?> 
         <?php if($uacf7_dismiss_post_notice == 1  || time() >  $uacf7_dismiss_post_notice ): ?>
            <style> 
                .uacf7_promo_side_preview a:focus {
                    box-shadow: none;
                } 
                .uacf7_promo_side_preview a {
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
                .uacf7_promo_side_preview {
                    position: relative;
                }
                .uacf7_promo_side_notice_dismiss {
                    position: ;
                    z-index: 1;
                }
            
            </style>
            
           
            <div class="uacf7_promo_side_preview" style="text-align: center; overflow: hidden; margin: 10px;">
                <a href="<?php echo esc_attr($deal_link); ?>" target="_blank" >
                    <img  style="width: 100%;" src="<?php echo esc_attr($image_url); ?>" alt="">
                </a>  
                <?php if( isset($dismiss_status) && $dismiss_status == true): ?>
                    <button type="button" class="notice-dismiss uacf7_promo_side_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                <?php  endif; ?>
                
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.uacf7_promo_side_notice_dismiss', function( event ) { 
                        jQuery('.uacf7_promo_side_preview').css('display', 'none')
                        data = {
                            action : 'uacf7_promo_side_notice_cf7_dismiss_callback', 
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

    public  function uacf7_promo_side_notice_cf7_dismiss_callback() {   
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
        delete_option('uacf7_promo__schudle_start_from');
    }
 
}

new UACF7_PROMO_NOTICE();
