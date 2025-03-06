(function ($) {
 
  document.addEventListener('wpcf7submit', function (e) {


    var form_id = e.detail.contactFormId;  
    if($('.uacf7-form-'+form_id).find('.wpcf7-uacf7_submission_id').length > 0){ 
      $.ajax({
        url : submission_id_obj.ajaxurl,
        type: 'POST',
        data: {
            action    : 'uacf7_update_submission_id',
            form_id   : form_id,
            ajax_nonce: submission_id_obj.nonce,
        },
        success: function(data) {

         $('.uacf7-form-'+form_id).find('.wpcf7-uacf7_submission_id').attr('value', data.meta_data);

        }
      });
    }

 

  }, true );
 

})(jQuery);