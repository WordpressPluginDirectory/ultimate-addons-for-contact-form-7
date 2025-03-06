jQuery( document ).ready( function(){
    
    jQuery('.tag-generator-panel-product-category').hide();
    jQuery('.tag-generator-panel-product-tag').hide();
    jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('name','');
    jQuery('.tag-generator-panel-product-tag #tag-generator-panel-product-tag').attr('name','');
    jQuery('.tag-generator-panel-select-layout-style #tag-generator-panel-select-layout-style').attr('style','');
    
    jQuery( 'input[name="product_by"]' ).on('change', function(){
        var product_by = jQuery( this ).val();

        if( product_by == 'id' ){

            jQuery('.tag-generator-panel-product-category, .tag-generator-panel-product-tag').hide();
            jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('data-tag-part','');
            jQuery('.tag-generator-panel-product-tag #tag-generator-panel-product-tag').attr('data-tag-part','');

            jQuery('.tag-generator-panel-product-id').show();
            jQuery('.tag-generator-panel-product-id #tag-generator-panel-product-id').attr('data-tag-part','value');

        }else if( product_by == 'category' ) {

            jQuery('.tag-generator-panel-product-category').show();
            jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('data-tag-part','value');

            jQuery('.tag-generator-panel-product-id, .tag-generator-panel-product-tag').hide();
            jQuery('.tag-generator-panel-product-id #tag-generator-panel-product-id').attr('data-tag-part','');
            jQuery('.tag-generator-panel-product-tag #tag-generator-panel-product-tag').attr('data-tag-part','');

        }else {

            jQuery('.tag-generator-panel-product-tag').show();
            jQuery('.tag-generator-panel-product-tag #tag-generator-panel-product-tag').attr('data-tag-part','value');

            jQuery('.tag-generator-panel-product-id, .tag-generator-panel-product-category').hide();
            jQuery('.tag-generator-panel-product-id #tag-generator-panel-product-id').attr('data-tag-part','');
            jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('data-tag-part','');

        }
    });
    
} );
