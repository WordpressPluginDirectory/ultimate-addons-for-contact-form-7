(function ($) {
    $(document).ready(function () {
        // Listen for clicks on the button that opens the dialog
        $(document).on('click', '[data-taggen="open-dialog"]', function () {
            var targetDialogId = $(this).data('target'); // Get the target dialog ID
            var $dialog = $('#' + targetDialogId); // Find the dialog element

            // Check if the dialog is for the "conditional" tag
            if ($dialog.find('form[data-id="conditional"]').length > 0) {
                var $tagInput = $dialog.find('input[data-tag-part="tag"]');

                // If the tag doesn't already end with "[/conditional]", append it
                if ($tagInput.val() && !$tagInput.val().endsWith("[/conditional]")) {
                    $tagInput.val($tagInput.val() + "[/conditional]");
                }
            }
        });
    });
})(jQuery);


// (function ($) {
//     'use strict';

//     if (_wpcf7 == null) {
//         var _wpcf7 = wpcf7;
//     }

//     var uacf7_compose = _wpcf7.taggen.compose;

//     _wpcf7.taggen.compose = function (tagType, $form) {

//         var uacf7_tag_close = uacf7_compose.apply(this, arguments);

//         if (tagType == 'conditional') uacf7_tag_close += "[/conditional]";

//         return uacf7_tag_close;
//     };

//     var cfList = document.getElementsByClassName("uacf7-cf").length;

//     var index = cfList;


// })(jQuery);