<?php 


// // Current url Shortcode
// if(!function_exists('UACF7_URL')){
//     function UACF7_URL($val){ 
//         beaf_print_r($val);
//         $data = get_permalink();
//         return $data;
//     }

//     add_shortcode('UACF7_URL', 'UACF7_URL'); 
// }

if (!function_exists('UACF7_URL')) {
    function UACF7_URL($val) {
        $current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $part = isset($val['part']) ? trim($val['part'], "'") : '';

        $parsed_url = parse_url($current_url);

        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_array);
            $decoded_query = http_build_query($query_array, '', '&', PHP_QUERY_RFC3986);
        } else {
            $decoded_query = '';
        }

        switch ($part) {
            case 'host':
                return $parsed_url['host'] ?? $current_url;
            case 'query':
                return urldecode($decoded_query);
            case 'path':
                return $parsed_url['path'] ?? '/';
            default:
                return $current_url;
        }
    }

    add_shortcode('UACF7_URL', 'UACF7_URL');
}




// Current url with Perameters Shortcode
if(!function_exists('UACF7_URL_WITH_PERAMETERS')){
  
    function UACF7_URL_WITH_PERAMETERS($val){ 
        $data = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $data;
    }

    add_shortcode('UACF7_URL_WITH_PERAMETERS', 'UACF7_URL_WITH_PERAMETERS'); 
}



// Blog Info Shortcode
if(!function_exists('UACF7_BLOGINFO')){
    function UACF7_BLOGINFO($val){ 
        if(!empty($val['attr'])){ 
           $data =  get_bloginfo($val['attr']); 
        }else{
            $data = get_bloginfo('name');
        }
        return $data;
    }
    add_shortcode('UACF7_BLOGINFO', 'UACF7_BLOGINFO');

}

// POST iNFO Info Shortcode
if(!function_exists('UACF7_POSTINFO')){
    function UACF7_POSTINFO($val){ 
        global $post; 
        $data = '';
        if($val['attr'] == 'post_permalink'){
            $data = get_permalink($post->ID);
        }elseif(!empty($val['attr'])){ 
            $post_attr = $val['attr'];
            $data =  $post->$post_attr;
        }else{
            $data = $post->post_title;
        }
        return $data;
    }
    add_shortcode('UACF7_POSTINFO', 'UACF7_POSTINFO');

}

// User Info Info Shortcode
if(!function_exists('UACF7_USERINFO')){
    function UACF7_USERINFO($val){  
        $data = '';
        if( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            if(!empty($val['attr'])){
                $user_attr = $val['attr'];
                $data = $current_user->$user_attr;
            }else{
                $data = $current_user->user_nicename;
            } 
        }
        return $data;
    }
    add_shortcode('UACF7_USERINFO', 'UACF7_USERINFO');

}

// Post Custom Fields Shortcode
if(!function_exists('UACF7_CUSTOM_FIELDS')){
    function UACF7_CUSTOM_FIELDS($val){    
        $data ='';

        if ( empty( $val['attr'] ) ) {
            return $data;
        }

        $value = explode("/",$val['attr']); 

        if ( count( $value ) === 2 ) {
            // Passed both ID and custom field
            $id = absint( $value[0] );
            $custom_field = sanitize_key( $value[1] );
        } else {
            // Only field name passed, get current post ID
            $id = get_the_ID();
            $custom_field = sanitize_key( $value[0] );
        }
        
        if ( $id > 0 && ! empty( $custom_field ) ) {
            $data = get_post_meta( $id, $custom_field, true );
        }

        return $data;
    }
    add_shortcode('UACF7_CUSTOM_FIELDS', 'UACF7_CUSTOM_FIELDS');
}


?>