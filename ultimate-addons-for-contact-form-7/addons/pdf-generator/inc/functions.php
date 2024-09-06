<?php
if (!defined('ABSPATH')) {
    exit;
}
 if(!function_exists('uacf7_pdf_form_tags_callback')){
    function uacf7_pdf_form_tags_callback($form_id){
        // get existing value 
        if($form_id > 0){
            $ContactForm = WPCF7_ContactForm::get_instance($form_id); 
            $all_fields = $ContactForm->scan_form_tags();
            ?>
            <h3> <strong><?php echo esc_html__( 'Form Tags :', 'ultimate-addons-cf7' ); ?>  </strong>
                <strong>
                    <?php
                        foreach ($all_fields as $tag) {
                            if ($tag['type'] != 'submit') {
                                echo '<span>['.esc_attr($tag['name']).']</span> ';
                            }
                        }
                    ?>
                </strong>
            </h3>
            <?php
        }
        
    }
 }

?>