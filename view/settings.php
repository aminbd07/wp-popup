
<div class="<?php echo $this->text_domain ?> settings"> 
    <h1>Global Settings </h1>
    <form method="post" id='wpsp-settings-form'>
        <div class=" form-area"> 

            <div class="form-group">
                <label> popup Overlay color  </label>
                <input type="text" data-default-color="" class="color-field form-control " name="popup_bg_color" value="<?php echo $this->global_setting['popup_bg_color'] ?>" />
            </div>
            <div class="form-group">
                <label> popup Overlay Opacity  </label>
                <div class="range-slider">
                    <input class="input-range" type="range" value="<?php echo $this->global_setting['popup_bg_opacity'] ?>" min="0" max="100">
                    <div class="rnage-input"> 
                        <input type="text" value="<?php echo $this->global_setting['popup_bg_opacity'] ?>"  class="range-value-udate" name="popup_bg_opacity"  />
                        % </div>
                </div>


                <label> Show Top Close button </label>
                <div class="switch-field">
                    <?php
                    $chcb_y = '';
                    $chcb_n = ''; 
                    if( $this->global_setting['show_close_btn'] == 'yes') { $chcb_y = 'checked'; }  
                    if( $this->global_setting['show_close_btn'] == 'no') { $chcb_n = 'checked' ; } 
                     
                    ?>
                    <input type="radio" id="switch_left" name="show_close_btn" value="yes" <?php echo $chcb_y ;?> />
                    <label for="switch_left">Yes</label>
                    <input type="radio" id="switch_right"  name="show_close_btn" value="no"  <?php echo $chcb_n?> />
                    <label for="switch_right">No</label>
                </div>

            </div>




            <div class="form-group">
                <label> Dialog box top margin (px or %)  </label>
                <input type="text" name="top_margin" class="form-control " value="<?php echo $this->global_setting['top_margin'] ?>" />
                
                  <label> Show popup Footer </label>
                   
                  <?php
                    $chsf_y = '';
                    $chsf_n = ''; 
                    if( $this->global_setting['show_footer'] == 'yes') $chsf_y = 'checked' ; 
                    if( $this->global_setting['show_footer'] == 'no') $chsf_n = 'checked' ; 
                    ?>
                  
                <div class="switch-field">
                    <input type="radio" id="switch_left2" name="show_footer" value="yes" <?php echo $chsf_y ?> />
                    <label for="switch_left2">Yes</label>
                    <input type="radio" id="switch_right2" name="show_footer" value="no" <?php echo $chsf_n ?> />
                    <label for="switch_right2">No</label>
                </div>
                  
            </div>
            
            
            <div class="form-group">
                
                  <label> Hide Popup for Post Excerpt </label>
                   
                  <?php
                    $chhe_y = '';
                    $chhe_n = ''; 
                    if( $this->global_setting['hide_in_excerpt'] == 'yes') $chhe_y = 'checked' ; 
                    if( $this->global_setting['hide_in_excerpt'] == 'no') $chhe_n = 'checked' ; 
                    ?>
                  
                <div class="switch-field">
                    <input type="radio" id="switch_left3" name="hide_in_excerpt" value="yes" <?php echo $chhe_y ?> />
                    <label for="switch_left3">Yes</label>
                    <input type="radio" id="switch_right3" name="hide_in_excerpt" value="no" <?php echo $chhe_n ?> />
                    <label for="switch_right3">No</label>
                </div>
                  
            </div>
            
            
             <br/>
             <br/>
            <div class="form-group"> 
             <label> popup Size   </label>
                <select name="popup_size"  class="form-control popup_size">
                    <option value="small" <?php if($this->global_setting['popup_size'] == 'small' ) echo 'selected' ?> >Small</option>
                    <option value="medium" <?php if($this->global_setting['popup_size'] == 'medium' ) echo 'selected' ?> >Medium</option>
                    <option value="large" <?php if($this->global_setting['popup_size'] == 'large' ) echo 'selected' ?> >Large</option>
                    <option value="extra-large"<?php if($this->global_setting['popup_size'] == 'extra-large' ) echo 'selected' ?> >Extra Large</option>
                    <option value="full-width"<?php if($this->global_setting['popup_size'] == 'full-width' ) echo 'selected' ?> >Full Width (100%) </option>
                    <option value="custom_size"<?php if($this->global_setting['popup_size'] == 'custom_size' ) echo 'selected' ?> > Custom Size </option>
                </select>
             
             <div class="popup_custom_width_area" style="display: <?php echo $this->global_setting['popup_size'] == 'custom_size' ? 'block':  'none' ?> ">
                 <input type="text" name="popup_custom_width" class="popup_custom_width" value="<?php echo $this->global_setting['popup_custom_width'] ?>" />
             </div>
            </div>
 
            <br/>

            <div  class="form-group text-center" >
                <input type="submit" name="submit" class="button button-primary button-large" value="Save" />
            </div>


        </div>

        <div class="clearfix"></div>


    </form>
     
    
    <div class="update-status" style="display: none"></div>
</div>