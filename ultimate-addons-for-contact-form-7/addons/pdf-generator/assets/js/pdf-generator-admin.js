;(function ($) {
    'use strict';
    $ ( document ).ready(function() {  
        
        $('.uacf7-db-pdf').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var form_id = $this.attr('data-form-id');
            var  id = $this.attr('data-id');
            var old_button_text = $this.html();
            $this.html('<img src="'+database_admin_url.plugin_dir_url+'assets/images/loader.gif" alt="">');
            jQuery.ajax({
                url: pdf_settings.ajaxurl,
                type: 'post',
                data: {
                    action: 'uacf7_get_generated_pdf',
                    form_id: form_id,
                    id: id,
                    ajax_nonce: pdf_settings.nonce,
                },
                success: function (data) {
                    $this.html(old_button_text);
                    if(data.status == 'success'){ 
                        // window.location.href = data.url; 
                        window.open(data.url, '_blank');
                    }else{
                        alert(data.message);
                    }
                
                }
            }); 
           
        });
    });  

})(jQuery);
