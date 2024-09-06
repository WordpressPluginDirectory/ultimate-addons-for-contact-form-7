(function ($) {



    var forms = $('.wpcf7-form');
    forms.each(function () {
        var formId = $(this).find('input[name="_wpcf7"]').val();
        var form_div = $(this).find('.uacf7-form-' + formId);
        var uacf7_spam_protection = $('.uacf7-form-' + formId).find('.uacf7_spam_recognation');

        var refreshButton = uacf7_spam_protection.find("#arithmathic_refresh");
        var validate = uacf7_spam_protection.find("#arithmathic_validate");
        var resultDiv = uacf7_spam_protection.find("#arithmathic_result");
        var protection_method = $(uacf7_spam_protection).attr('protection-method');



        // Generating Random Numbers
        function uacf7_generate_ramdom_numbers() {
            var first_random_number = Math.random() * 10;
            var second_random_number = Math.random() * 10;
            uacf7_spam_protection.find('#arithmathic_recognation').find('#frn').text(Math.ceil(first_random_number));
            uacf7_spam_protection.find('#arithmathic_recognation').find('#srn').text(Math.ceil(second_random_number));
        }
        uacf7_generate_ramdom_numbers();



        //Returning Total Sum of Numbers
        function return_total_num() {
            var first_number = uacf7_spam_protection.find('#arithmathic_recognation').find('#frn').text();
            var first_number_int = parseInt(first_number);
            var second_number = uacf7_spam_protection.find('#arithmathic_recognation').find('#srn').text();
            var second_number_int = parseInt(second_number);
            var total_number = first_number_int + second_number_int;
            return total_number;
        }



        //Refresh button action
        refreshButton.click(function (e) {
            $(this).find('svg').addClass('spin-rotate');
            setTimeout(() => {
                $(this).find('svg').removeClass('spin-rotate');
            }, 1000);
            e.preventDefault();
            uacf7_spam_protection.find("#rtn").val('');
            uacf7_generate_ramdom_numbers();
            resultDiv.text('');

        });


        //Conditionally make submission event false
        $(window).on('load', function () {

            var form_submit = uacf7_spam_protection.closest(`.uacf7-form-${formId}`).find('.wpcf7-submit');

            form_submit.on('click', function (e) {

                var userInput = uacf7_spam_protection.find("#rtn").val();
                var resultDiv = uacf7_spam_protection.find("#arithmathic_result");
                const isSpamProtectionProEnabled = uacf7_spam_protection_settings.enable_spam_protection_pro;
                // Check if userInput is empty
                if (typeof userInput !== 'undefined' && userInput.trim() === '') {
                    refreshButton.trigger('click'); // Refresh CAPTCHA
                    // Field is empty, set warning message and prevent form submission
                    resultDiv.text(uacf7_spam_protection_settings.captchaARequiredMessage).css("color", "#DC2626");
                    e.preventDefault(); // Prevent form submission
                    return;
                } else {
                    // If it's not empty, compare it with the expected value
                    var expectedTotal = return_total_num(); // Fetch the correct answer for CAPTCHA
                    if (userInput == expectedTotal) {
                        if (!isSpamProtectionProEnabled) {
                            // If it matches, perform the success actions
                            refreshButton.trigger('click');
                        }
                        resultDiv.text(uacf7_spam_protection_settings.captchaValidatedMessage).css("color", "#46b450");
                    } else {
                        // If it does not match, set a failure message and prevent form submission
                        e.preventDefault(); // Prevent form submission
                        refreshButton.trigger('click'); // Refresh CAPTCHA
                        resultDiv.text(uacf7_spam_protection_settings.captchaValidationFailed).css("color", "#DC2626");
                        return false;
                    }
                }

            });


        });


    });

})(jQuery);




