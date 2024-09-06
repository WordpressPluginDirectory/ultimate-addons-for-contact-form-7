(function ($) {
    $(document).ready(function () {
        var uacf_quick_preloader = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="20x" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
        <path d="M10 50A40 40 0 0 0 90 50A40 42 0 0 1 10 50" fill="#ffffff" stroke="none">
          <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 51;360 50 51"></animateTransform>
        </path>`;

        // One Click CF7 Plugin install and activate
        $(document).on('click', '.required-plugin-button', function () {
            var plugin_status = $(this).attr('data-plugin-status');
            if (plugin_status == 'not_installed') {
                var plugin_slug = 'contact-form-7';
                var button = $(this);
                button.html('Installing...');
                $.ajax({
                    url: uacf7_admin_params.ajax_url,
                    type: 'post',
                    data: {
                        action: 'contact_form_7_ajax_install_plugin',
                        _ajax_nonce: uacf7_admin_params.uacf7_nonce,
                        slug: plugin_slug,
                    },
                    success: function (response) {
                        $('.required-plugin-button').attr('data-plugin-status', 'not_active');
                        uacf7_onclick_ajax_activate_plugin()
                    }
                });
            } else if (plugin_status == 'not_active') {
                uacf7_onclick_ajax_activate_plugin();
            }

        });

        function uacf7_onclick_ajax_activate_plugin() {
            var button = $('.required-plugin-button');
            var plugin_slug = 'contact-form-7';
            var file_name = 'wp-contact-form-7';
            button.html('Activating...');
            $.ajax({
                url: uacf7_admin_params.ajax_url,
                type: 'post',
                data: {
                    action: 'uacf7_onclick_ajax_activate_plugin',
                    _ajax_nonce: uacf7_admin_params.uacf7_nonce,
                    slug: plugin_slug,
                    file_name: file_name,
                },
                success: function (response) {
                    button.html('Activated');
                    $('.required-plugin-button').attr('data-plugin-status', 'activate');
                    $('.required-plugin-button').attr('disabled', true);
                    $('.required-plugin-button').addClass('disabled');
                    $('.uacf7-next').attr('disabled', false);
                    $('.uacf7-next').removeClass('disabled');
                    $('.uacf7-next').trigger('click');
                }
            });
        }


        // select 2
        $('#select2').select2({
            placeholder: 'Select a form type',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: -1,
        });

        // Uacf7 Next Button
        $(document).on('click', '.uacf7-next', function (e) {

            var $this = $(this);
            var current_step = $this.attr('data-current-step');
            var next_step = $this.attr('data-next-step');
            $('.uacf7-single-step-content[data-step=' + current_step + ']').removeClass('active');
            $('.uacf7-single-step-content[data-step=' + next_step + ']').addClass('active');
            $('.uacf7-single-step-item[data-step=' + next_step + ']').addClass('active');
            $('.uacf7-single-step-item[data-step=' + current_step + '] .step-item-dots').html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0965 7.39016L9.9365 14.3002L8.0365 12.2702C7.6865 11.9402 7.1365 11.9202 6.7365 12.2002C6.3465 12.4902 6.2365 13.0002 6.4765 13.4102L8.7265 17.0702C8.9465 17.4102 9.3265 17.6202 9.7565 17.6202C10.1665 17.6202 10.5565 17.4102 10.7765 17.0702C11.1365 16.6002 18.0065 8.41016 18.0065 8.41016C18.9065 7.49016 17.8165 6.68016 17.0965 7.38016V7.39016Z" fill="#7F56D9"/>
            </svg> `);
            $('.uacf7-single-step-item[data-step=' + next_step + '] .step-item-dots').html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="24" height="24" rx="12" fill="#F9F5FF"/>
            <circle cx="12" cy="12" r="4" fill="#7F56D9"/>
            </svg>  `);
            $this.attr('data-current-step', next_step);
            $this.attr('data-next-step', parseInt(next_step) + 1);

            if (current_step == '2') {
                $this.hide();
                $('.wizard_uacf7_btn_back_addon').show();
                $this.addClass('skip');
                // only replace next to skip without svg icon
                $this.html('Skip' + `<svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.3337 4.99951L1.66699 4.99951" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9.00051 8.33317C9.00051 8.33317 12.3338 5.87821 12.3338 4.99981C12.3338 4.12141 9.00049 1.6665 9.00049 1.6665" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`);
                // $(".tf-option-form.tf-ajax-save").submit();

            } else {
                $('.uacf7-next').show();
                $this.show();
                $this.removeClass('skip');
                // only replace next to skip without svg icon
                $this.html('Next' + `<svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.3337 4.99951L1.66699 4.99951" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.00051 8.33317C9.00051 8.33317 12.3338 5.87821 12.3338 4.99981C12.3338 4.12141 9.00049 1.6665 9.00049 1.6665" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>`);
            }
        });

        // Wizard process bar single items on click Event
        $(document).on('click', '.uacf7-single-step-item', function (e) {
            $this = $(this);
            var current_step = $this.attr('data-step');
            var next_step = parseInt(current_step) + 1;
            var preb_step = parseInt(current_step) - 1;
            if (current_step != 4) {

                if (current_step == '2') {

                    if ($(".required-plugin-button").hasClass('disabled') == false) {
                        $(".required-plugin-button").trigger('click');
                        return;
                    }

                }

                $('.uacf7-single-step-item[data-step="' + current_step + '"]').nextAll('.uacf7-single-step-item').removeClass('active');

                $('.uacf7-single-step-item[data-step="' + current_step + '"]').prevAll('.uacf7-single-step-item').addClass('active');
                $('.uacf7-single-step-item[data-step="' + current_step + '"]').addClass('active');
                $('.uacf7-single-step-content').removeClass('active');
                $('.uacf7-single-step-content[data-step="' + current_step + '"]').addClass('active');
                $('.uacf7-single-step-item[data-step=' + preb_step + '] .step-item-dots').html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0965 7.39016L9.9365 14.3002L8.0365 12.2702C7.6865 11.9402 7.1365 11.9202 6.7365 12.2002C6.3465 12.4902 6.2365 13.0002 6.4765 13.4102L8.7265 17.0702C8.9465 17.4102 9.3265 17.6202 9.7565 17.6202C10.1665 17.6202 10.5565 17.4102 10.7765 17.0702C11.1365 16.6002 18.0065 8.41016 18.0065 8.41016C18.9065 7.49016 17.8165 6.68016 17.0965 7.38016V7.39016Z" fill="#7F56D9"/>
                    </svg> `);
                $('.uacf7-single-step-item[data-step=' + current_step + '] .step-item-dots').html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="24" height="24" rx="12" fill="#F9F5FF"/>
                    <circle cx="12" cy="12" r="4" fill="#7F56D9"/>
                    </svg>  `);
                $('.uacf7-next').attr('data-current-step', current_step);
                $('.uacf7-next').attr('data-next-step', parseInt(current_step) + 1);

                if (current_step == '3') {
                    $('.wizard_uacf7_btn_back_addon').show();
                    $('.uacf7-next').hide();

                    // only replace next to skip without svg icon
                    $('.uacf7-next').html('Skip' + `<svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12.3337 4.99951L1.66699 4.99951" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9.00051 8.33317C9.00051 8.33317 12.3338 5.87821 12.3338 4.99981C12.3338 4.12141 9.00049 1.6665 9.00049 1.6665" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>`);
                    // $(".tf-option-form.tf-ajax-save").submit();

                } else {
                    $('.wizard_uacf7_btn_back_addon').hide();
                    $('.uacf7-next').show();
                    $('.uacf7-next').removeClass('skip');
                    // only replace next to skip without svg icon
                    $('.uacf7-next').html('Next' + `<svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.3337 4.99951L1.66699 4.99951" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.00051 8.33317C9.00051 8.33317 12.3338 5.87821 12.3338 4.99981C12.3338 4.12141 9.00049 1.6665 9.00049 1.6665" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>`);
                }
            }
        });

        // Click event to open wizard form select dropdown
        $(document).on('click', '.uacf7-single-step-item[data-step="3"]', function (e) {
            $('#uacf7-select-form').select2();
            $('#uacf7-select-form').select2('open');
        });

        // Uacf7 Create Form
        $(document).on('change', '#uacf7-select-form', function (e) {
            if ($(this).val() != '') {
                $('.uacf7-generate-form').show();
            } else {
                $('.uacf7-generate-form').hide();
            }
        });

        // Uacf7 Create Form
        $(document).on('click', '.uacf7-create-form', function (e) {
            e.preventDefault();
            var $this = $(this);
            var form_name = $('#uacf7-select-form').val();
            var form_value = $('#uacf7_ai_code_content').val();
            if (form_name.length <= 1) {
                alert('Please select form type');
                return false;
            }

            $.ajax({
                url: uacf7_admin_params.ajax_url,
                type: 'post',
                data: {
                    action: 'uacf7_form_quick_create_form',
                    form_name: form_name,
                    form_value: form_value,
                    _ajax_nonce: uacf7_admin_params.uacf7_nonce,
                },
                success: function (data) {
                    if (data.status == 'success') {
                        // redirect to edit page
                        window.location.href = data.edit_url;
                    } else {
                        console.log(data.message)
                    }
                }
            });
        });

        // Uacf7 Generate Form
        $(document).on('click', '.uacf7-generate-form', function (e) {
            e.preventDefault();
            $('.uacf7-single-step-item.step-last').addClass('active');
            $('.uacf7-single-step-item[data-step="3"] .step-item-dots').html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0965 7.39016L9.9365 14.3002L8.0365 12.2702C7.6865 11.9402 7.1365 11.9202 6.7365 12.2002C6.3465 12.4902 6.2365 13.0002 6.4765 13.4102L8.7265 17.0702C8.9465 17.4102 9.3265 17.6202 9.7565 17.6202C10.1665 17.6202 10.5565 17.4102 10.7765 17.0702C11.1365 16.6002 18.0065 8.41016 18.0065 8.41016C18.9065 7.49016 17.8165 6.68016 17.0965 7.38016V7.39016Z" fill="#7F56D9"/>
            </svg>`);
            $('.uacf7-single-step-item.step-last .step-item-dots').html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="24" height="24" rx="12" fill="#F9F5FF"/>
            <circle cx="12" cy="12" r="4" fill="#7F56D9"/>
            </svg>`);
            var $this = $(this);
            var searchValue = $('#uacf7-select-form').val();
            if (searchValue.length <= 1) {
                alert('Please select form type');
                return false;
            }

            $.ajax({
                url: uacf7_admin_params.ajax_url,
                type: 'post',
                data: {
                    action: 'uacf7_form_generator_ai_quick_start',
                    searchValue: searchValue,
                    _ajax_nonce: uacf7_admin_params.uacf7_nonce,
                },
                success: function (data) {
                    $('.uacf7-single-step-content-inner img').hide();
                    $('.uacf7-generated-template').show();
                    $('#uacf7_ai_code_content').val('');
                    typeName(data.value, 0);

                }
            });
        });


        function typeName(data, iteration) {
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

    });

})(jQuery);