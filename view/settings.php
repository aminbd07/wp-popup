
<div class="<?php echo $this->text_domain ?> settings"> 
    <h1>Global Settings </h1>
    <form method="post" id='wpobm-settings-form'>
        <div class=" form-area"> 

            <div class="form-group">
                <label> Modal Overlay color  </label>
                <input type="text" data-default-color="" class="color-field form-control " name="modal_bg_color" value="<?php echo $this->global_setting['modal_bg_color'] ?>" />
            </div>
            <div class="form-group">
                <label> Modal Overlay Opacity  </label>
                <div class="range-slider">
                    <input class="input-range" type="range" value="<?php echo $this->global_setting['modal_bg_opacity'] ?>" min="0" max="100">
                    <div class="rnage-input"> 
                        <input type="text" value="<?php echo $this->global_setting['modal_bg_opacity'] ?>"  class="range-value-udate" name="modal_bg_opacity"  />
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
                
                  <label> Show Modal Footer </label>
                   
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
             <br/>
             <br/>
            <div class="form-group"> 
             <label> Modal Size   </label>
                <select name="modal_size" class="form-control ">
                    <option value="small" <?php if($this->global_setting['modal_size'] == 'small' ) echo 'selected' ?> >Small</option>
                    <option value="medium" <?php if($this->global_setting['modal_size'] == 'medium' ) echo 'selected' ?> >Medium</option>
                    <option value="large" <?php if($this->global_setting['modal_size'] == 'large' ) echo 'selected' ?> >Large</option>
                    <option value="extra-large"<?php if($this->global_setting['modal_size'] == 'extra-large' ) echo 'selected' ?> >Extra Large</option>
                    <option value="full-width"<?php if($this->global_setting['modal_size'] == 'full-width' ) echo 'selected' ?> >Full Width (100%) </option>
                </select>
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