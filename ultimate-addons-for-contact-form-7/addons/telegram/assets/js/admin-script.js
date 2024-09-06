(function ($) {

    var token = $("#uacf7_form_opt\\[telegram\\]\\[uacf7_telegram_bot_token\\]").val();

    function getBotUsername(token) {
        // Check if the token is not empty
        if (!token) {
            return;
        }

        try {
            $.ajax({
                url: "https://api.telegram.org/bot" + token + "/getMe",
                method: "GET",
                success: function (response) {
                    if (response.ok) {
                        const botName = response.result.first_name;
                        const botUsername = response.result.username;
                        $('.online').css('display', 'block');
                        $('.offline').css('display', 'none');
                        $('.bot_info').css({
                            display: 'block',
                            marginTop: '10px'
                        });
                        $('.bot_name').html('<strong>Bot Name:</strong> ' + botName).css('display', 'block');
                        $('.bot_username').html('<strong>Bot Username:</strong> @' + botUsername).css('display', 'block');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error here
                    console.error('Error fetching bot information:', error);
                }
            });
        } catch (error) {
            console.error('Error in getBotUsername function:', error);
        }
    }

    getBotUsername(token);
})(jQuery);
