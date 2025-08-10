;(function ($) {
    'use strict';

    $(document).ready(function () {

        // Handle pre-population via URL
        var urlParams = new URLSearchParams(window.location.search);
        var form_id = urlParams.get('form');

        if (form_id) {
            const groupedParams = {};

            for (const [key, value] of urlParams.entries()) {
                if (key.endsWith('[]')) {
                    const plainKey = key.slice(0, -2);
                    if (!groupedParams[plainKey]) groupedParams[plainKey] = [];
                    groupedParams[plainKey].push(value);
                } else {
                    groupedParams[key] = value;
                }
            }

            for (const key in groupedParams) {
                const value = groupedParams[key];

                if (Array.isArray(value)) {
                    // Checkbox array
                    value.forEach(val => {
                        $(`form [name='${key}[]'][value="${decodeURIComponent(val)}"]`)
                            .prop('checked', true)
                            .trigger('change');
                    });
                } else {
                    const input = $(`form [name='${key}']`);
                    const inputType = input.attr('type');

                    if (inputType === 'radio' || inputType === 'checkbox') {
                        $(`form [name='${key}'][value="${decodeURIComponent(value)}"]`)
                            .prop('checked', true)
                            .trigger('change');
                    } else if (input.is('select')) {
                        input.val(decodeURIComponent(value)).trigger('change');
                    } else {
                        input.val(decodeURIComponent(value)).trigger('keyup');
                    }

                    // UACF7 Repeater support
                    const repeaterInput = $(`form [uacf-original-name='${key}']`);
                    if (repeaterInput.length) {
                        repeaterInput
                            .val(decodeURIComponent(value))
                            .attr('uacf-field-type', 'pre-populate')
                            .trigger('keyup');
                    }
                }
            }
        }

        // On submit, handle redirect URL building
        $(".wpcf7-submit").click(function (e) {
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
                    if (data !== false) {
                        var fields = data.pre_populate_passing_field;
                        var redirect_url = '?form=' + encodeURIComponent(data.pre_populate_form);

                        if (data.pre_populate_enable == 1) {
                            fields.forEach(function (field_name) {
                                var input = form.find("[name='" + field_name + "']");
                                var value = '';

                                if (input.length) {
                                    if (input.attr('type') === 'radio' || input.attr('type') === 'checkbox') {
                                        value = form.find("[name='" + field_name + "']:checked").val();
                                    } else {
                                        value = input.val();
                                    }
                                }

                                if (value) {
                                    redirect_url += '&' + encodeURIComponent(field_name) + '=' + encodeURIComponent(value);
                                }
                            });

                            document.addEventListener('wpcf7mailsent', function (event) {
                                if (event.detail.status === 'mail_sent') {
                                    location.href = data.data_redirect_url + redirect_url;
                                }
                            }, false);
                        }
                    }
                }
            });
        });
        
    });

})(jQuery);
