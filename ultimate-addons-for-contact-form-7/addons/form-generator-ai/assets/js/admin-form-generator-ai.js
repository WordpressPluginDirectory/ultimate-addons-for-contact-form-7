(function ($) {


  $(document).ready(function () {
    // Add form generator ai in wpcf7-new page and edit page
    $('#uacf7-form-generator-ai').each(function () {
      // $this variable 
      var $this = $(this);
      // First option  
      var first_option = [
        { value: 'form', label: 'Form' },
        { value: 'tag', label: 'Tag' },
      ]; 

      // Second option for Form
      var secend_option_form = [];
      // Second option for Tag
      var secend_option_tag = [];
    

      // Third  option for Tag
      var third_option = [];

      // Fourth  option for Tag
      var fourth_option = [];

      // Initialize Choices
      const uacf_form_ai = new Choices('#uacf7-form-generator-ai', {
        maxItemCount: 5,
        disabled: false,
        allowHTML: false,
        duplicateItemsAllowed: false,
        placeholderValue: 'Describe your form',
        // searchPlaceholderValue: 'This is a search placeholder',
      }).setChoices(first_option, 'value', 'label', true);

      // Add form generator ai On change customize option
      $('#uacf7-form-generator-ai').on('change', function (event) {
        var current_values = uacf_form_ai.getValue();     
        // Clear all choices
        uacf_form_ai.clearChoices();
        if (current_values.length == 1 ) {
          if(secend_option_tag.length == 0 || secend_option_form.length == 0){
            $('.choices__inner').append('<img class="uacf7_ai_loader" src="' + uacf7_form_ai.loader + '" alt="loader">'); 
            $.ajax({
              url: uacf7_form_ai.ajaxurl,
              type: 'post',
              data: {
                action: 'uacf7_form_generator_ai_get_tag',
                ajax_nonce: uacf7_form_ai.nonce,
                data_value: current_values[0].value,
              },
              success: function (data) {
                $('.choices__inner img').remove();
                secend_option_form = data.value_form;
                secend_option_tag = data.value_tag; 

                if(current_values[0].value == 'form'){
                  uacf_form_ai.setChoices(secend_option_form, 'value', 'label', true);
                }else{
                  uacf_form_ai.setChoices(secend_option_tag, 'value', 'label', true);
                }
              }
            }); 
          }else{
            if(current_values[0].value == 'form'){
              uacf_form_ai.setChoices(secend_option_form, 'value', 'label', true);
            }else{
              uacf_form_ai.setChoices(secend_option_tag, 'value', 'label', true);
            }
          }
          
        }
        else if (current_values.length == 2) {
          if (current_values[0].value == 'tag' && (current_values[1].value == 'uacf7-col') ){
            // Third  option for Tag
            var third_option = [
              { value: 'col-1', label: '1 Column' }, 
              { value: 'col-2', label: '2 Column' }, 
              { value: 'col-3', label: '3 Column' }, 
              { value: 'col-4', label: '4 Column' }, 
            ]; 
          }else if(current_values[0].value == 'tag'){
             // Third  option for Tag
              var third_option = [
                { value: 'label', label: 'With Label' }, 
              ];  
          }
          uacf_form_ai.setChoices(third_option, 'value', 'label', true);
        }
        else if (current_values.length == 3) {
          if (current_values[0].value == 'tag'){
            // Third  option for Tag
            var fourth_option = [
              { value: '*', label: 'Required' },
            ]; 
          } 
          uacf_form_ai.setChoices(fourth_option, 'value', 'label', true); 
        }
        else if (current_values.length == 0) {
          uacf_form_ai.setChoices(first_option, 'value', 'label', true);
        }

      });

      // Reset form generator ai On click reset button
      $('.uacf7-ai-code-reset').on('click', function (event) { 
        $('#uacf7_ai_code_content').val(''); 
      });

      // Copy to clipboard using jquery
      $('.uacf7-ai-code-copy').on('click', function (event) { 
        if($('#uacf7_ai_code_content').val() != ''){
          var $temp = $('<textarea>');
          $("body").append($temp);
          $temp.val($('#uacf7_ai_code_content').val()).select();
          document.execCommand("copy");
          $temp.remove();
          
          alert('Form copied to clipboard!');
        }else{
          alert('Please generate form first!');
        }
         
      });

      // Insert form generator ai On click insert button into form editor
      $('.uacf7-ai-code-insert').on('click', function (event) {
        var textToCopy = $('#uacf7_ai_code_content').val();
        $('#wpcf7-form').val(textToCopy);

        // Close popup
        $('.uacf7-form-ai-popup').removeClass('active');
      });

    });



    // Add Button into UACF7 form editor
    var fullURL = window.location.href; 
    // Create a URL object
    var parsedUrl = new URL(fullURL);
    
    // Use URLSearchParams to get values
    var pageValue = parsedUrl.searchParams.get('page'); 
    var actionValue = parsedUrl.searchParams.get('action');
    var activeTab = parsedUrl.searchParams.get('active-tab'); 
 
    // display form generator ai in wpcf7-new page and edit page
    if (pageValue == 'wpcf7-new' || (pageValue == 'wpcf7' && actionValue == 'edit') || (pageValue == 'wpcf7' && activeTab == 0 || activeTab > 0)){
      $($(".wrap h1")[0]).append("<a  id='uacf7-ai-form-button-popup' class='uacf7-ai-form-button'>Form Generator AI</a>");

      $(document).on('click', '#uacf7-ai-form-button-popup', function (e) {
        $('.uacf7-form-ai-popup').addClass('active');

      });
      $(document).on('click', '.uacf7-form-ai-popup .close', function (e) {
        $('.uacf7-form-ai-popup').removeClass('active');

      });

      var substringMatcher = function (strs) {
        return function findMatches(q, cb) {
          var matches, substringRegex;

          // an array that will be populated with substring matches
          matches = [];

          // regex used to determine if a string contains the substring `q`
          substrRegex = new RegExp(q, 'i');

          // iterate through the pool of strings and for any string that
          // contains the substring `q`, add it to the `matches` array
          $.each(strs, function (i, str) {
            if (substrRegex.test(str)) {
              matches.push(str);
            }
          });

          cb(matches);
        };
      };
    }


    // Generate form Using Ajax Query

  });

  $(document).on('click', '.uacf7_ai_search_button', function (e) {
    e.preventDefault();
    // $('#uacf7_ai_code_content pre code').html(html); 
    var $this = $(this);
    var searchValue = $('#uacf7-form-generator-ai').val();  
    if(searchValue.length <= 1){
      alert('Please select form type');
      return false;
    }
    
    $this.attr('disabled', true);
    $this.append('<img class="uacf7_ai_loader" src="' + uacf7_form_ai.loader + '" alt="loader">');
    $.ajax({
      url: uacf7_form_ai.ajaxurl,
      type: 'post',
      data: {
        action: 'uacf7_form_generator_ai',
        searchValue: searchValue, 
        ajax_nonce: uacf7_form_ai.nonce,
      },
      success: function (data) { 
        typeName(data.value, 0); 
        
        $this.find('.uacf7_ai_loader').remove();
        $this.attr('disabled', false);
      }
    });
  });

  function typeName(data, iteration ) {
    // Prevent our code executing if there are no letters left
    if (iteration === data.length)
      return;

    setTimeout(function () {
      // Set the name to the current text + the next character
      // whilst incrementing the iteration variable

      $('#uacf7_ai_code_content').val($('#uacf7_ai_code_content').val() + data[iteration++]);
      // Re-trigger our function
      typeName(data, iteration);
      var textarea = $("#uacf7_ai_code_content");
      textarea.scrollTop(textarea[0].scrollHeight);
    }, 5);
 
  }
})(jQuery);


