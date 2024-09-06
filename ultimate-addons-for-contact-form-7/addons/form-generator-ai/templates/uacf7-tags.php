<?php
defined( 'ABSPATH' ) || exit;

/**
 * Template for the Form Generator AI tags.
 *
 * @package   UACF7
 * @subpackage Form Generator AI
 * @since     1.0.0
 * @Author:  Sydur Rahman
 * @variable :  $uacf7_default, $form_step, $form_field, $form_label
 *
 */ 
$manager = WPCF7_FormTagsManager::get_instance();

// $reflector = new ReflectionClass('WPCF7_TagGenerator');
// $property = $reflector->getProperty('panels');
// $property->setAccessible(true);

// $panels = $property->getValue($tag_generator); 

ob_start();
    $field = '';
    if(isset($uacf7_default[1]) && !empty($uacf7_default[1])){
        $form_label = isset($uacf7_default[2]) ? $uacf7_default[2] : 0;
        $required = isset($uacf7_default[3]) ? $uacf7_default[3] : '';
        $number = rand(100, 999);
        
        switch($uacf7_default[1]){
            case 'text':
                $tag = '[text'.esc_attr($required).' text-'.$number.' placeholder "Your text here"]';
                break;
            case 'email':
                $tag = '[email'.esc_attr($required).' email-'.$number.' placeholder "you@example.com"]';
                break;
            case 'url':
                $tag = '[url'.esc_attr($required).' url-'.$number.' placeholder "https://example.com"]';
                break;
            case 'tel':
                $tag = '[tel'.esc_attr($required).' tel-'.$number.' placeholder "+1 (555) 123-4567"]';
                break;
            case 'number':
                $tag = '[number'.esc_attr($required).' number-'.$number.' min:0 max:100 step:1 placeholder "Enter a number"]';
                break;
            case 'number':
                $tag = '[date'.esc_attr($required).' date-'.$number.' min:2023-09-20 max:2024-09-20 step:1]';
                break;
            case 'number':
                $tag = '[textarea'.esc_attr($required).' textarea-'.$number.' placeholder "Your message here..."]';
                break;
            case 'menu':
                $tag = '[select'.esc_attr($required).' menu-'.$number.' "Option 1" "Option 2" "Option 3"]';
                break;
            case 'checkbox':
                $tag = '[checkbox'.esc_attr($required).' checkbox-'.$number.' "Option 1" "Option 2" "Option 3"]';
                break;
            case 'radio':
                $tag = '[radio radio-'.$number.' default:1 "Option 1" "Option 2" "Option 3"]';
                break;
            case 'acceptance':
                $tag = '[acceptance acceptance-'.$number.'] I agree to the terms and conditions. [/acceptance]';
                break;
            case 'quiz':
                $tag = '[quiz'.esc_attr($required).' quiz-'.$number.' "What is the capital of France?|Paris"]';
                break;
            case 'file':
                $tag = '[file'.esc_attr($required).' file-'.$number.' limit:2mb filetypes:jpg|jpeg|png|pdf]';
                break;
            case 'submit':
                $tag = '[submit "Send"]';
                break;
            case 'uacf7_city':
                $tag = '[uacf7_city'.esc_attr($required).' uacf7_city-'.$number.' placeholder:City]';
                break;
            case 'uacf7_state':
                $tag = '[uacf7_state'.esc_attr($required).' uacf7_state-'.$number.' placeholder:State]';
                break;
            case 'uacf7_zip':
                $tag = '[uacf7_zip'.esc_attr($required).' uacf7_zip-'.$number.' placeholder:Zip Code]';
                break;
            case 'uacf7_product_dropdown':
                $tag = '[uacf7_product_dropdown'.esc_attr($required).' uacf7_product_dropdown-'.$number.']';
                break;
            case 'uacf7_star_rating':
                $tag = '[uacf7_star_rating'.esc_attr($required).' rating-'.$number.' selected:5 star1:1 star2:2 star3:3 star4:4 star5:5 icon:star1 "default"]';
                break;
            case 'uacf7_range_slider':
                $tag = '[uacf7_range_slider'.esc_attr($required).' uacf7_range_slider-'.$number.' min:15 max:100 default:50 step:1 show_value:on handle:1 "default"]';
                break;
            case 'uacf7_country_dropdown':
                $tag = '[uacf7_country_dropdown'.esc_attr($required).' uacf7_country_dropdown-'.$number.']';
                break; 
            case 'uacf7_submission_id':
                $tag = '[uacf7_submission_id uacf7_submission_id-'.$number.']';
                break; 
            default : 
                $tag = '['.$uacf7_default[1].' '.$uacf7_default[1].'-'.$number.']';
                break; 
        } 

        if($form_label == 'label'){ 
            $field =  '<label> '.$uacf7_default[1].' '.PHP_EOL. $tag.' </label>' . PHP_EOL;
        }else{
            $field = $tag.PHP_EOL;
        }

        if($uacf7_default[1] == 'uacf7-col' && isset($uacf7_default[2])  ){
            switch ($uacf7_default[2]) { 
                case "col-2":
                    $field = '[uacf7-row] '.PHP_EOL.' [uacf7-col col:6] --your code-- [/uacf7-col]'.PHP_EOL.' [uacf7-col col:6] --your code-- [/uacf7-col]'.PHP_EOL.' [/uacf7-row]'.PHP_EOL;
                    break;
                case "col-3";
                    $field = '[uacf7-row]'.PHP_EOL.' [uacf7-col col:4] --your code-- [/uacf7-col]'.PHP_EOL.' [uacf7-col col:4] --your code-- [/uacf7-col] '.PHP_EOL.'   [uacf7-col col:4] --your code-- [/uacf7-col]'.PHP_EOL.'[/uacf7-row]'.PHP_EOL;
                    break;
                case "col-4":
                    $field = '[uacf7-row]'.PHP_EOL.' [uacf7-col col:3] --your code-- [/uacf7-col]'.PHP_EOL.' [uacf7-col col:3] --your code-- [/uacf7-col]'.PHP_EOL.'   [uacf7-col col:3] --your code-- [/uacf7-col]'.PHP_EOL.' [uacf7-col col:3] --your code-- [/uacf7-col] '.PHP_EOL.'[/uacf7-row]'.PHP_EOL;
                    break;
                default:
                    $field = '[uacf7-row] '.PHP_EOL.'  [uacf7-col col:12] --your code-- [/uacf7-col] '.PHP_EOL.'  [/uacf7-row]'.PHP_EOL;
                    break;
            }
        }
    }
    echo $field;
    


 return ob_get_clean();