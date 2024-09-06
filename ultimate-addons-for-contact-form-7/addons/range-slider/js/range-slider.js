; (function ($) {
  'use strict';
  $(document).ready(function () {
    $('.uacf7-slider.uacf7-range').each(function () {
      
      $(document).on('input', '.uacf7-slider.uacf7-range', function(){ 
          $(this).parent().parent().find('.uacf7-value').html($(this).val());
          
        }); 
    });
    function  multistep_range_slider() {
      $( ".multistep_slide" ).each(function() {
        var handle = $(this).parent().parent().find(".uacf7-slider-handle").data("handle"); 
        var min = $(this).parent().parent().find(".uacf7-slider-handle").data("min");
        
        var step = $(this).parent().parent().find(".uacf7-slider-handle").data("step");
        var max = $(this).parent().parent().find(".uacf7-slider-handle").data("max");
        var separator = $(this).parent().parent().find(".uacf7-slider-handle").data("separator");
        var label = $(this).parent().parent().find(".uacf7-slider-handle").data("label");
        var def = $(this).parent().parent().find(".uacf7-slider-handle").data("default");
        if (handle == 2) { 
            $(this).slider({
              range: true,
              min: min,
              step: step,
              max: max,
              values: [min, def],
              slide: function (event, ui) {
                $(this).parent().parent().find("#uacf7-amount").val(ui.values[0] +  " "+label+" " + " "+separator+" " + ui.values[1] +  " "+label+" ");
                $(this).parent().parent().find(".uacf7-amount").html(ui.values[0] +  " "+label+" " + " "+separator+" " + ui.values[1] +  " "+label+" " );
              }
            });
            $(this).parent().parent().find("#uacf7-amount").val($(this).slider("values", 0) + " - " + $(this).slider("values", 1));
            $(this).parent().parent().find(".uacf7-amount").val($(this).slider("values", 0) + " - " + $(this).slider("values", 1));
        }
      });
    }
    multistep_range_slider();

    // For Repeater Range Slider
    $(document).on('click', '.uacf7_repeater_add', function(){ 
      multistep_range_slider();
    });
    $( ".mutli_range_slide" ).each(function() {
      var handle = $(this).parent().parent().find(".uacf7-slider-handle").data("handle");
      var min = $(this).parent().parent().find(".uacf7-slider-handle").data("min");
      var step = $(this).parent().parent().find(".uacf7-slider-handle").data("step");
      var style = $(this).parent().parent().find(".uacf7-slider-handle").data("style");
      var max = $(this).parent().parent().find(".uacf7-slider-handle").data("max");
      var def = $(this).parent().parent().find(".uacf7-slider-handle").data("default");
      if (handle == 2) { 
           $(this).slider({
            range: true,
            min: min,
            step: step,
            max: max,
            values: [min, def],
            slide: function (event, ui) {
              $(this).parent().parent().find("#uacf7-amount").val(ui.values[0] + " - " + ui.values[1]); 
              $(this).parent().parent().find(".min-value-"+style+"").html(ui.values[0]);
              $(this).parent().parent().find(".max-value-"+style+"").html(ui.values[1]);
            }
          });
          $(this).parent().parent().find("#uacf7-amount").val($(this).slider("values", 0) + " - " + $(this).slider("values", 1));
          $(this).parent().parent().find(".uacf7-amount").val($(this).slider("values", 0) + " - " + $(this).slider("values", 1));
      }
    }); 
  }) 
})(jQuery);