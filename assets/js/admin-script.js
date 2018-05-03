;
(function ($) {

    // Add Color Picker to all inputs that have 'color-field' class
    $(function () {
        $('.color-field').wpColorPicker();

        jQuery('#wpobm-settings-form').on('submit', function (e) {
            e.preventDefault();
            var form_data = $("#wpobm-settings-form").serialize();
            $.ajax({
                url: WPOBM_Vars.ajaxurl,
                type: 'post',
                data: {
                    action: 'wpobm_settings_save',
                    security: WPOBP_Vars.nonce,
                    form_data: form_data,
                },
                success: function (response) {

                    $(".update-status").html(response); 
                    $('.update-status').show(500).delay(5000).hide(500);


                }
            });
        });
        
       
        jQuery('#wpobm-theme').on('submit', function (e) {
            e.preventDefault();
            var form_data = $("#wpobm-theme").serialize();
            $.ajax({
                url: WPOBM_Vars.ajaxurl,
                type: 'post',
                data: {
                    action: 'wpobp_update_theme_save',
                    security: WPOBP_Vars.nonce,
                    form_data: form_data,
                },
                success: function (response) {

                    $(".update-status").html(response); 
                    $('.update-status').show(500).delay(5000).hide(500);

                }
            });
        });
        
        
        
         // Change 
        jQuery("body").on("change", ".input-range, .range-value-udate", function(){
            var val = jQuery(this).val();  
            jQuery(".range-value-udate").val(val);
            jQuery(".input-range").val(val);
            
        }); 
        
        
         // Theme Change 
        jQuery(".theme-change").on("change", function(){
            var val = jQuery(this).val(); 
            $("#wpob_modal_backend_style-css").attr({href : WPOBM_Vars.pluginurl+"/assets/css/theme/"+val+".css"});
            
        }); 
        
         // Theme customization click
        jQuery(".customize_theme").on("click", function(){
            var chk = jQuery('.customize_theme').is(":checked") ;
            var dtca = 'none'; 
            if(chk){
                 dtca = 'block'; 
            } 
            
            jQuery('.customize_theme_area').css('display', dtca); 
            
        }); 

    });

})(jQuery);