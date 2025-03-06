;(function ($) { 
    'use strict';  
        $(document).ready(function() { 
          $(".wpcf7-submit").click(function(e){ 
              var form = $(this).closest("form");
              var form_id = form.find('input[name="_wpcf7"]').val();
      
              jQuery.ajax({
                  url: pre_populate_url.ajaxurl,
                  type: 'post',
                  data: {
                      action: 'uacf7_ajax_pre_populate_redirect',
                      form_id: form_id,
                      ajax_nonce: pre_populate_url.nonce,
                  },
                  success: function (data) {
                      if(data != false){ 
                          var shifting_field = data.pre_populate_passing_field; 
                          var redirect_data = '?form=' + encodeURIComponent(data.pre_populate_form); 
                          var pre_populate_enable = data.pre_populate_enable;
      
                          if(pre_populate_enable == 1){
                              shifting_field.forEach(function(field_name) {
                                  var input = form.find("[name='" + field_name + "']");
                                  var value = '';
      
                                  if(input.length > 0) {
                                      if(input.attr('type') === 'radio' || input.attr('type') === 'checkbox') { 
                                          value = form.find("[name='" + field_name + "']:checked").val();
                                      } else { 
                                          value = input.val();
                                      }
                                  }
      
                                  if (value) {
                                      redirect_data += '&' + encodeURIComponent(field_name) + '=' + encodeURIComponent(value);
                                  }
                              });
      
                              document.addEventListener('wpcf7mailsent', function (event) {
                                  if (event.detail.status == 'mail_sent') {
                                      location.href = data.data_redirect_url + redirect_data; // Redirect final location
                                  }
                              }, false); 
                          }
                      } 
                  }
              }); 
          }); 
      });

      $ ( document ).ready(function() { 
        var form_id = new URLSearchParams(window.location.search).get('form');  // Get data form url 
        if(form_id != '' && form_id != 0){ // if url parameter is not Blank 
            var url = document.location.href; // get current url

            var value = url.substring(url.indexOf('?') + 1).split('&'); // get current url parameter

            for(var i = 0, result = {}; i < value.length; i++){ 
                
                value[i] = value[i].split('='); 

                var type = $("form [name='"+value[i][0]+"']").attr('type');  // input type checked
                var multiple = $("form [name='"+value[i][0]+"[]']").attr('type'); // input type checked
                
                if(type == 'radio' || type == 'checkbox'){   
                  $("form [name='"+value[i][0]+"'][value="+decodeURIComponent(value[i][1])+"]").attr("checked", true); 
                  $("form [name='"+value[i][0]+"'][value="+decodeURIComponent(value[i][1])+"]").trigger('keyup'); 
              }else if( multiple == 'checkbox' ){  
                  $("form [name='"+value[i][0]+"[]'][value="+decodeURIComponent(value[i][1])+"]").attr("checked", true);
                  $("form [name='"+value[i][0]+"[]'][value="+decodeURIComponent(value[i][1])+"]").trigger('keyup');  
              }else{
                $("form [name='"+value[i][0]+"']").attr('value', decodeURIComponent(value[i][1])); 
                $("form [name='"+value[i][0]+"']").trigger('keyup');  
              }

              //Pre populated data with repeater addon

              if($("form [uacf-original-name='"+value[i][0]+"']")){
                $("form [uacf-original-name='"+value[i][0]+"']").attr('value', decodeURIComponent(value[i][1])); 
                $("form [uacf-original-name='"+value[i][0]+"']").attr('uacf-field-type','pre-populate'); 
                $("form [uacf-original-name='"+value[i][0]+"']").trigger('keyup'); 
              }
            } 
        }  
    });
})(jQuery);
