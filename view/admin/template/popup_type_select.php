<div id="titlewrap">
    <b>Popup Type </b>
    <ul class="select_popup_type" data-postid="<?php echo $post->ID; ?>">
        <li>
            <label> 
                <input type="radio" name="popup_type" value="html"  <?php echo  ($metadata['popup_type'] == 'html' || $metadata['popup_type'] == '' ) ? 'checked' : ''; ?>  />   
                <img src="<?php echo WPSP_URL ?>/assets/images/form/html.svg" />
                <p> 
                    <b>HTML</b><br/>
                    Any type of HTML tag working here, expect Video, iframe. </p>
            </label>
        </li>

        <li>
            <label> 

                <input type="radio" name="popup_type" value="image"  <?php echo $metadata['popup_type'] == 'image' ? 'checked' : ''; ?> />   
                <img src="<?php echo WPSP_URL ?>/assets/images/form/image.svg" />                               
                <p class="help">
                    <b>Image </b> <br/>
                    Popup type image, you can seletc an image for popup content. 
                </p>
            </label>
        </li>



        <li>
            <label> 
                <input type="radio" name="popup_type" value="facebook"  <?php echo $metadata['popup_type'] == 'facebook' ? 'checked' : ''; ?> />   
                <img src="<?php echo WPSP_URL ?>/assets/images/form/facebook.svg" />
                <p>
                    <b>Face Book Like Page</b><br/>
                    You can add your page page feed. 
                </p>
            </label>
        </li>



        <li>
            <label> 
                <input type="radio" name="popup_type" value="shortcode"  <?php echo $metadata['popup_type'] == 'shortcode' ? 'checked' : ''; ?> />   
                <img src="<?php echo WPSP_URL ?>/assets/images/form/shortcode.svg" />
                <p>
                    <b>Short Code</b><br/>
                    You can add any type of short code here. 
                </p>
            </label>
        </li>

        <li>
            <label> 
                <input type="radio" name="popup_type" value="youtube"  <?php echo $metadata['popup_type'] == 'youtube' ? 'checked' : ''; ?> />   
                <img src="<?php echo WPSP_URL ?>/assets/images/form/youtube.svg" />

                <p>
                    <b>YouTube Video</b><br/>
                    You can embed YouTube video here.
                </p>
            </label>
        </li>



        <br clear="all"/>
    </ul>

    <div class="selected_info" ></div>

    <div class="loading_content" style="display: none">
        <img src="<?php echo WPSP_URL; ?>/assets/images/loading.gif" />
    </div>
    <div class="custom_popup_input"></div>

</div>
<script>

    jQuery(function () {
        var selected = jQuery('input[name=popup_type]:checked');

        var tetx = jQuery(selected).parent().find("p").html();
        tetx = "<p class='help'>" + tetx + "</p>";
        jQuery(".selected_info").html(tetx);
        jQuery(".selected_info").show(500);

        var img = jQuery(selected).parent().find("img");
        var post_id = jQuery('.select_popup_type').attr('data-postid'); 
        swicth_popup_type(img, post_id);
        //
    });

</script>