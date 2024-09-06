(function ($) {
    //Conditionally make submission event false
    $(window).on('load', function () {
        var forms = $('.wpcf7-form');
        forms.each(function () {
            var formId = $(this).find('input[name="_wpcf7"]').val();
            var uacf7_spam_protection = $('.uacf7-form-' + formId).find('.uacf7_spam_recognation');
            var form_div = $(this).find('.uacf7-form-' + formId);
            const refreshButton = form_div.find("#arithmathic_refresh");
            const captcha = form_div.find("#captcha");
            const validate = form_div.find("#validate");
            let protection_method = $(uacf7_spam_protection).attr('protection-method');
            const isSpamProtectionProEnabled = uacf7_spam_protection_settings.enable_spam_protection_pro;

            //Generate Image captcha
            const captchaCodes = [];

            function generateRandomString(length) {
                const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!#$%&()*+,-./:;< = >?@[\]^_{|}~";
                let result = '';
                for (let i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * characters.length));
                }
                return result;
            }

            for (let i = 0; i < 50; i++) {
                const code = generateRandomString(6);
                captchaCodes.push(code);
            }

            function generateCaptcha() {
                const randomIndex = Math.floor(Math.random() * captchaCodes.length);
                const captcha = captchaCodes[randomIndex];
                form_div.find("#captcha").text(captcha);
            }
            generateCaptcha();

            //Refresh button action
            refreshButton.click(function (e) {
                $(this).find('svg').addClass('spin-rotate');
                setTimeout(() => {
                    $(this).find('svg').removeClass('spin-rotate');
                }, 1000);
                e.preventDefault();
                generateCaptcha();
                form_div.find("#userInput").val('');
                const resultDiv = form_div.find("#result");
                resultDiv.text('');

            });

            const form_submit = form_div.find('.wpcf7-submit');

            form_submit.on('click', function (e) {
                const userInput = form_div.find("#userInput").val();
                const resultDiv = form_div.find("#result");

                // Check if userInput is empty
                if (typeof userInput !== 'undefined' && userInput.trim() === '') {
                    // Field is empty, set warning message and prevent form submission
                    e.preventDefault(); // Prevent form submission
                    // Refresh CAPTCHA
                    refreshButton.trigger('click');
                    resultDiv.text(uacf7_spam_protection_settings.captchaRequiredMessage).css("color", "#DC2626");
                    return false;
                } else {
                    // If it's not empty, compare it with the expected value
                    const captcha = form_div.find("#captcha").text(); // Fetch the correct answer for CAPTCHA

                    if (userInput == captcha) {
                        if (!isSpamProtectionProEnabled) {
                            // If it matches, perform the success actions
                            refreshButton.trigger('click');
                        }
                        resultDiv.text(uacf7_spam_protection_settings.captchaSuccessMessage).css("color", "#46b450");
                    } else {
                        // If it does not match, set a failure message and prevent form submission
                        e.preventDefault();
                        refreshButton.trigger('click'); // Refresh CAPTCHA
                        resultDiv.text(uacf7_spam_protection_settings.captchaFailedMessage).css("color", "#DC2626");
                        return false;
                    }
                }
            });

        });

    });





})(jQuery);

