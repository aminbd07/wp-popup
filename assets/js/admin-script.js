;
(function ($) {

    // Add Color Picker to all inputs that have 'color-field' class
    $(function () {
        $('.color-field').wpColorPicker();

        jQuery('#wpsp-settings-form').on('submit', function (e) {
            e.preventDefault();
            var form_data = $("#wpsp-settings-form").serialize();
            $.ajax({
                url: WPSP_Vars.ajaxurl,
                type: 'post',
                data: {
                    action: 'wpsp_settings_save',
                    security: WPSP_Vars.nonce,
                    form_data: form_data,
                },
                success: function (response) {

                    $(".update-status").html(response);
                    $('.update-status').show(500).delay(5000).hide(500);


                }
            });
        });


        jQuery('#wpsp-theme').on('submit', function (e) {
            e.preventDefault();
            var form_data = $("#wpsp-theme").serialize();
            $.ajax({
                url: WPSP_Vars.ajaxurl,
                type: 'post',
                data: {
                    action: 'wpsp_update_theme_save',
                    security: WPSP_Vars.nonce,
                    form_data: form_data,
                },
                success: function (response) {

                    $(".update-status").html(response);
                    $('.update-status').show(500).delay(5000).hide(500);

                }
            });
        });



        // Change 
        jQuery("body").on("change", ".input-range, .range-value-udate", function () {
            var val = jQuery(this).val();
            jQuery(".range-value-udate").val(val);
            jQuery(".input-range").val(val);

        });


        // Popup size Change 
        jQuery(".popup_size").on("change", function () {
            var val = jQuery(this).val();

            if (val == 'custom_size') {
                jQuery(".popup_custom_width_area").show(300);
            } else {
                jQuery(".popup_custom_width_area").hide();
            }

        });



        // Theme Change 
        jQuery(".theme-change").on("change", function () {
            var val = jQuery(this).val();
            $("#wps_popup_backend_style-css").attr({href: WPSP_Vars.pluginurl + "/assets/css/theme/" + val + ".css"});

        });

        // Theme customization click
        jQuery(".customize_theme").on("click", function () {
            var chk = jQuery('.customize_theme').is(":checked");
            var dtca = 'none';
            if (chk) {
                dtca = 'block';
            }

            jQuery('.customize_theme_area').css('display', dtca);

        });

        // Theme Change 
        jQuery(".select_popup_type li label img ").on("click", function () {
            var post_id = jQuery('.select_popup_type').attr('data-postid'); 
            swicth_popup_type(this, post_id) ; 
        });

        jQuery(".select_popup_type li label span ").on("hover", function () {
            var val = jQuery(this).parent().find('.help').html();

            jQuery(".selected_info").html(val);
            jQuery(".selected_info").show(500);

        });




    });

})(jQuery);

function swicth_popup_type(img, post_id){
     
    var val = jQuery(img).next('p').html();
            jQuery(".selected_info").html('');
            jQuery(img).parent().find('input[name=popup_type]').attr('checked', true);

            var selected = jQuery('input[name=popup_type]:checked').val();
            jQuery(".selected_info").html("<p class='help'>" + val + "</p>");
            jQuery(".selected_info").show(500);


            if (selected === 'html') {
                jQuery(".postarea").show();
                jQuery(".custom_popup_input").hide();

            } else {
                jQuery(".postarea").hide();
                jQuery(".loading_content").show();
                jQuery(".custom_popup_input").html('');
                jQuery.ajax({
                    url: WPSP_Vars.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'wpsp_show_custom_popup_input',
                        security: WPSP_Vars.nonce,
                        post_ID: post_id,
                        popuptype: selected,
                    },
                    success: function (response) {
                        jQuery(".loading_content").hide();
                        jQuery(".custom_popup_input").show().html(response);
                    }
                });
            }

}