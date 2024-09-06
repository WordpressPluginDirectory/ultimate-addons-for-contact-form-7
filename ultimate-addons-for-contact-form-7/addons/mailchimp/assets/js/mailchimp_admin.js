(function ($) {
    $(document).ready(function () {

        // MailChimp automatic connection 
        const glop_mailchimp = $('#mailchimp').find('.tf-field-password').find('.tf-fieldset').find('input[id="uacf7_settings\\[uacf7_mailchimp_api_key\\]"]');

        glop_mailchimp.on('change', function (event) {
            event.preventDefault();

            console.log('call');
            const main_id = $('#mailchimp');
            const inputKey = $(this).val();  // Correctly get the value of the input field
            const status_div = main_id.find('.tf-field.tf-field-callback').find('.tf-field-notice-inner');

            // Check if the preloader exists; if not, append it
            if ($('#preloader').length === 0) {
                main_id.append('<div id="preloader" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 8px solid #f3f3f3; border-radius: 50%; border-top: 8px solid #3498db; width: 60px; height: 60px; animation: spin 2s linear infinite;"></div>');
            }

            // Function to show the preloader
            function showPreloader() {
                $('#preloader').show();
            }

            // Function to hide the preloader
            function hidePreloader() {
                $('#preloader').hide();
            }

            // Show preloader before making the API call
            showPreloader();

            $.ajax({
                url: mailchimp_peram.ajaxurl,
                type: 'post',
                data: {
                    action: 'uacf7_ajax_mailchimp',
                    ajax_nonce: mailchimp_peram.nonce,
                    inputKey: inputKey  // Fix the key name to match PHP handler
                },
                success: function (data) {
                    // Assuming 'data' contains the status you want to display
                    status_div.html(data.data.status);
                    hidePreloader(); // Hide preloader after the API call completes
                },
                error: function (xhr, status, error) {
                    status_div.html('AJAX error: ' + error);  // Correctly display the error message
                    hidePreloader(); // Hide preloader after the API call completes
                }
            });
        });

        // Toggle password visibility
        const mailchimp_toggle_password = $('#mailchimp').find('.tf-field-password').find('.tf-fieldset').find('.toggle-password');
        mailchimp_toggle_password.on('click', function () {
            const passwordField = $(this).siblings('input[id="uacf7_settings\\[uacf7_mailchimp_api_key\\]"]');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);

            // Toggle the icon
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });


    });

    //get UACF7_Mailchimp
    // function mailchimp_Api() {
    //     if ($('#mailchimp').length > 0) {
    //         var mailchimp_input = $('#mailchimp').find('.tf-field-text input');
    //         var originalValue = mailchimp_input.val();

    //         function maskValue(value) {
    //             if (value.length <= 10) {
    //                 // If the string is 10 characters or less, show the entire string
    //                 return value;
    //             }

    //             var firstFive = value.substring(0, 5);
    //             var lastFive = value.substring(value.length - 5);
    //             var middlePart = value.substring(5, value.length - 5).replace(/./g, '*');
    //             return firstFive + middlePart + lastFive;
    //         }

    //         var maskedValue = maskValue(originalValue);
    //         // console.log(maskedValue);
    //         // Optionally set the masked value back to the input
    //         mailchimp_input.val(maskedValue);
    //     }
    // }
    // mailchimp_Api();

})(jQuery);