<div class="popup-settings-side-bar">
    <div class="form-group"> 
        <label class="title">
            Load popup on 
        </label> 
        <label>
            <input type="radio" name="load_on" value="onload" <?php if($metadata['load_on'] == 'onload') echo 'checked'; ?>  onclick="return switch_mode()" /> On Page load 
        </label>
        <label>
            <input type="radio" name="load_on" value="onclick"  <?php if($metadata['load_on'] == 'onclick') echo 'checked'; ?> onclick="return switch_mode()" /> On Link click 
        </label>
         <label style="">
            <input type="radio" name="load_on" value="scrool"  <?php if($metadata['load_on'] == 'scrool') echo 'checked'; ?> disabled/> On Scrool (Pro Version, coming soon)  
        </label>
    </div>

    <div class="form-group link-settings" style="display: none"> 
        <label class="title ">
            Show Popup button as  
        </label> 
        <label>
            <input type="radio" name="show_as" value="button" <?php if($metadata['show_as'] == 'button') echo 'checked'; ?>  /> Button
        </label>
        <label>
            <input type="radio" name="show_as" value="link" <?php if($metadata['show_as'] == 'link') echo 'checked'; ?>  /> Link
        </label>
       


        <label class="title ">
            Button Style 
        </label> 
        <label>
            Button Color  </label>
        <input type="text" data-default-color="" class="color-field form-control " name="button_color" value="<?php echo $metadata['button_color'] ?>" />

        <label>
            Button border Radius 
        </label> 
        <div class="range-slider">
            <input class="input-range" type="range" value="<?php echo $metadata['button_border_radious'] ?>" min="0" max="100">
            <div class="rnage-input"> 
                <input type="text"   class="range-value-udate" name="button_border_radious" value="<?php echo $metadata['button_border_radious'] ?> " />px 
            </div>
        </div>

    </div>

    <div class="form-group onload-settings" style="display: none"> 
         <label class="title ">
             Load after
        </label> 
        <input type="text" name="load_after" value="<?php echo $metadata['load_after'] ?>" size="5"  /> Seconds  
        
    </div>
</div>

<script>
    switch_mode();

 
    function switch_mode() {
        var poponload = jQuery('input[name=load_on]:checked').val();

        if (poponload === 'onload') {
            jQuery('.link-settings').hide();
            jQuery('.onload-settings').show();
        } else {
            jQuery('.link-settings').show();
            jQuery('.onload-settings').hide();
        }

    }

</script>