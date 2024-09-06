;(function ($) {
    'use strict';
    
    if (_wpcf7 == null) {
        var _wpcf7 = wpcf7;
    }

    var uacf7_compose = _wpcf7.taggen.compose;

    _wpcf7.taggen.compose = function ( tagType, $form ) {

        var uacf7_tag_close = uacf7_compose.apply( this, arguments );

        if (tagType == 'conditional') uacf7_tag_close += "[/conditional]";

        return uacf7_tag_close;
    };

    var cfList = document.getElementsByClassName("uacf7-cf").length;

    var index = cfList;
 
    
})(jQuery);