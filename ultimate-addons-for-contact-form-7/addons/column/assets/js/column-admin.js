(function ($) {
    'use strict';


	
	jQuery(document).on( 'click', '.uacf7-column-select', function(){
		jQuery(this).siblings().removeClass('example-active');
		jQuery(this).addClass('example-active');
		
		var uacf7ColumnTag = jQuery(this).attr('data-column-codes');
		jQuery('.uacf7-column-tag-insert').val(uacf7ColumnTag);
		
		jQuery('.insert-tag.uacf7-column-insert-tag').trigger('click');
	});
	
	//Custom column
	jQuery(document).on('click', '.add-custom-column', function() {
		var field = '<div class="column-width-wraper"><input type="text" class="column-width" placeholder="Enter column width"> <span>(E.g: 50% or 200px)</span> <a class="remove-column">x</a></div>';
		jQuery('.uacf7-custom-column').append( field );
		
	});
	
	jQuery(document).on('click', '.column-width-wraper .remove-column', function() {
		jQuery(this).parent('.column-width-wraper').remove();
	});

})(jQuery);
