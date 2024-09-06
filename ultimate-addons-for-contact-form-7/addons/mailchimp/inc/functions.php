<?php
if (!defined('ABSPATH')) {
    exit;
}
 if(!function_exists('uacf7_mailchimp_api_status_callback')){
    function uacf7_mailchimp_api_status_callback($status){
        echo '<div class="tf-field-notice-inner tf-notice-info">';
        echo $status;
        echo '</div>';
    }
 } 