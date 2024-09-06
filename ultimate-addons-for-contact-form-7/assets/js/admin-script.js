(function ($) {
    $(function () {
        // Add Color Picker to all inputs that have 'color-field' class
        // $('.tf-color').wpColorPicker();
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.uacf7-color-picker').wpColorPicker();
        }
    });

    $(document).ready(function () {

        // Create an instance of Notyf
        const notyf = new Notyf({
            ripple: true,
            dismissable: true,
            duration: 3000,
            position: {
                x: 'right',
                y: 'bottom',
            },
        });

        function uacf7_backup_filed_copy(textarea) {
            // Check if the Clipboard API is supported
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textarea.val())
                    .then(function () {
                        console.log("Text copied to clipboard.");
                        notyf.success("Text copied to clipboard.");
                    })
                    .catch(function (err) {
                        console.error("Error copying text to clipboard:", err);
                    });
            } else {
                console.warn("Clipboard API is not supported. Consider copying manually.");

                // Provide a fallback for manual copy
                textarea.select();
                alert("Clipboard copy is not supported. Please use Ctrl+C (Cmd+C on Mac) to copy the text.");
            }
        }

        // Import and Export Option
        function initializeImportExportFunctions() {
            const backupfields = $('#import_export').find('.tf-field-backup .tf-fieldset');
            const exportArea = backupfields.find('.tf-export-field');
            const uACF7SettingExportButton = backupfields.find('.tf-export-button');
            const copyIndicator = backupfields.find('#copyIndicator');

            // Ensure the textarea is enabled
            if (exportArea.is(':disabled')) {
                exportArea.prop('disabled', false);
            }

            // Ensure when textarea gets hover showing copy text
            exportArea.hover(function () {
                copyIndicator.text('Click to copy');
                copyIndicator.css({ 'display': 'block' });
            }, function () {
                copyIndicator.text('');
                copyIndicator.css({ 'display': 'none' });
            });

            // Clean up existing click event handlers to avoid duplication
            copyIndicator.hover(function () {
                copyIndicator.text('Click to copy');
                copyIndicator.css({ 'display': 'block' });
            }, function () {
                copyIndicator.text('');
                copyIndicator.css({ 'display': 'none' });
            });

            copyIndicator.off('click');
            copyIndicator.on('click', function (e) {
                uacf7_backup_filed_copy(exportArea);
            });

            // Clean up existing click event handlers to avoid duplication
            exportArea.off('click');
            exportArea.on('click', function (event) {
                event.preventDefault();
                var textarea = $(this);

                // Call the copyer function
                uacf7_backup_filed_copy(textarea);

                // Re-disable the textarea if necessary
                textarea.prop('disabled', true);
            });

            // Clean up existing click event handlers to avoid duplication for Export button
            uACF7SettingExportButton.off('click');
            uACF7SettingExportButton.on('click', function (event) {
                event.preventDefault();

                var textarea = $('.tf-export-field');

                // Call the copyer function
                uacf7_backup_filed_copy(textarea);

                // Re-disable the textarea if necessary
                textarea.prop('disabled', true);
            });
        }

        // Import and Export option 
        initializeImportExportFunctions();

        // Clean up existing click event handlers to avoid duplication for Global Export button
        const globalbackup = $('#uacf7_import_export').find('.tf-field-backup .tf-fieldset');
        const globalButton = globalbackup.find('.tf-export-button');
        globalButton.off('click');
        globalButton.on('click', function (event) {
            event.preventDefault();
            var textarea = $('.tf-export-field');

            // Call the copyer function
            uacf7_backup_filed_copy(textarea);

            // Re-disable the textarea if necessary
            textarea.prop('disabled', true);
        });

    });

})(jQuery);



function uacf7_settings_tab(event, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("uacf7-tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName(" tablinks ");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("active", "");
    }
    document.getElementById(tabName).style.display = "block";
    event.currentTarget.className += " active";
}


//Add style to all UACF7 tags
jQuery('.thickbox.button').each(function () {
    var str = jQuery(this).attr('href');

    if (str.indexOf("uacf7") >= 0) {
        jQuery(this).css({ "backgroundColor": "#487eb0", "color": "white", "border-color": "#487eb0" });
    }
    if (str.indexOf("uarepeater") >= 0) {
        jQuery(this).css({ "backgroundColor": "#487eb0", "color": "white", "border-color": "#487eb0" });
    }
    if (str.indexOf("conditional") >= 0) {
        jQuery(this).css({ "backgroundColor": "#487eb0", "color": "white", "border-color": "#487eb0" });
    }
});

//Multistep script
jQuery(document).ready(function () {
    uacf7_progressbar_style();
});
jQuery('#uacf7_progressbar_style').on('change', function () {
    uacf7_progressbar_style();
});
function uacf7_progressbar_style() {
    if (jQuery('#uacf7_progressbar_style').val() == 'default' || jQuery('#uacf7_progressbar_style').val() == 'style-1') {
        jQuery('.multistep_field_column.show-if-pro').hide();
    } else {
        jQuery('.multistep_field_column.show-if-pro').show();
    }

    if (jQuery('#uacf7_progressbar_style').val() == 'style-2' || jQuery('#uacf7_progressbar_style').val() == 'style-3' || jQuery('#uacf7_progressbar_style').val() == 'style-6') {
        jQuery('.multistep_field_column.show-if-left-progressbar').show();
    } else {
        jQuery('.multistep_field_column.show-if-left-progressbar').hide();
    }

    if (jQuery('#uacf7_progressbar_style').val() == 'style-6') {
        jQuery('.multistep_field_column.show-if-style-6').show();
    } else {
        jQuery('.multistep_field_column.show-if-style-6').hide();
    }

    if (jQuery('#uacf7_progressbar_style').val() == 'style-6') {
        jQuery('.step-title-description').show();
    } else {
        jQuery('.step-title-description').hide();
    }
}