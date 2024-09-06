jQuery(document).ready(function ($) {

    // signature function for load the signature
    function uacf7_signature_load() {
        var forms = $(".wpcf7");
        var signs = [];

        forms.each(function (k, form) {
            var formId = $(this).find('input[name="_wpcf7"]').val();
            var fileInput = $('.uacf7-form-' + formId).find('.img_id_special');

            fileInput.css('display', 'none');

            var data = [];
            var pad_bg_color = fileInput.attr('bg-color');
            var pen_color = fileInput.attr('pen-color');

            $(form).find(".signature-pad").each(function (i, wrap) {
                var canvas = $(wrap).find('canvas').get(0);
                var signaturePad = new SignaturePad(canvas, {
                    includeBackgroundColor: true,
                    backgroundColor: pad_bg_color,
                    penColor: pen_color,
                });


                signs[k + '-' + i] = signaturePad;
                signs[k + '-' + i].addEventListener('endStroke', function (e) {
                    data = signaturePad.toDataURL('image/png');

                    var field_id = $(wrap).attr('data-field-name');
                    var input_id = $('input[name="' + field_id + '"]');
                    const image = new Image();

                    image.src = data;
                    image.setAttribute('class', 'uacf7-Uacf7UploadedImageForSign');
                    image.setAttribute('data-field-name', field_id);

                    document.body.appendChild(image);

                    const imagePreview = document.querySelector('img[data-field-name="' + field_id + '"]');
                    const dataUrl = imagePreview.src;
                    const blob = dataURLtoBlob(dataUrl);
                    const fileName = 'signature' + field_id + '.jpg';
                    const file = new File([blob], fileName);
                    const fileList = new DataTransfer();

                    fileList.items.add(file);
                    input_id.prop("files", fileList.files);
                    image.remove();

                    function dataURLtoBlob(dataUrl) {
                        const parts = dataUrl.split(';base64,');
                        const contentType = parts[0].split(':')[1];
                        const raw = window.atob(parts[1]);
                        const rawLength = raw.length;
                        const uint8Array = new Uint8Array(rawLength);

                        for (let i = 0; i < rawLength; ++i) {
                            uint8Array[i] = raw.charCodeAt(i);
                        }

                        return new Blob([uint8Array], { type: contentType });
                    }
                });

                canvas.style.cursor = "crosshair";

                //Clearing after form Submission
                $('.uacf7-form-' + formId).find('.wpcf7-submit').click(function () {
                    signaturePad.clear();
                });

            });

            // Uacf7 signature clear function handler
            $('.clear-button').click(function (e) {
                e.preventDefault();
                var signature_canvas = $(this).closest('.wpcf7-form-control-wrap').find('.signature-pad').find('canvas').get(0);
                var canvas_file_input_field = $(this).closest('.wpcf7-form-control-wrap').find('input[type="file"]');

                canvas_file_input_field.val('');

                var signaturePadInstance = new SignaturePad(signature_canvas, {
                    includeBackgroundColor: true,
                    backgroundColor: pad_bg_color,
                    penColor: pen_color,
                });

                signaturePadInstance.clear();
                signs = [];

            });


            /** Preventing file system opening */
            $('.uacf7-form-' + formId).find('.img_id_special').click(function (e) {
                e.preventDefault();
            });

        });
    }

    uacf7_signature_load();

    // Recall the signature function if repeater addon repeat
    $(document).on('click', '.uacf7_repeater_add', function () {
        uacf7_signature_load();
    });

});