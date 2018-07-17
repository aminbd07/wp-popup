<?php
$fb_meta = get_post_meta($post->ID, $this->text_domain . '_fb_settings', TRUE);
$fb_meta = unserialize($fb_meta);
?>
<div>
    <div class="form-group">
    <label> Text before Facebook widget </label>  <br/>

    <textarea placeholder="Popup Body Content" rows="6" cols="80" name="popup_fb"  ><?php echo $popup_data ?></textarea>
    </div>

    <div id="" class=" postbox popup_type_settings" style="display: block;">
        <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Slug</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Facebook Settings </span></h2>
        <div class="inside">
            <div class="form-group"> 
                <label class="" >Facebook Page URL</label>
                <input name="popup_fb_url" type="text" size="13" id="" value="<?php echo $fb_meta['popup_fb_url'] ?>"> 
            </div>

            <div class="form-group">
                <label class="" > Width </label>
                <input name="popup_fb_width" type="text" size="13" id="" value="<?php echo $fb_meta['popup_fb_width'] ?>"  class="small">px
            </div>

            <div class="form-group">
                <label class="" > Height </label>
                <input name="popup_fb_height" type="text" size="13" id="" value="<?php echo $fb_meta['popup_fb_height'] ?>"  class="small">px
            </div>

            <div class="form-group">
                <label class="" > Page Tab  </label>
                <?php
                    $tads = unserialize($fb_meta['popup_fb_tab']) ;
                ?>
                <label class="no-fixed">
                    <input type="checkbox" value="timeline" name="popup_fb_tab[]" <?php echo (in_array('timeline', $tads )) ? 'checked' : ''?>   > Timeline
                </label>
                <label class="no-fixed">
                    <input type="checkbox" value="events" name="popup_fb_tab[]"  <?php echo (in_array('events', $tads )) ? 'checked' : ''?>  > Events
                </label>
                <label class="no-fixed">
                    <input type="checkbox" value="messages" name="popup_fb_tab[]"  <?php echo (in_array('messages', $tads )) ? 'checked' : ''?>  > Messages
                </label>

            </div>



            

            <div class="form-group"> 
                <label class="" >Hide Cover Photo </label>
                <select name="popup_fb_hide_cover">
                    <option value="1" <?php echo ('1' == $fb_meta['popup_fb_hide_cover']) ? 'selected' : ''; ?> >Yes </option>
                    <option value="0" <?php echo ('0'==$fb_meta['popup_fb_hide_cover'] ) ? 'selected' : ''; ?> >No </option>

                </select>
            </div>

            <div class="form-group">
                <label class="" > Small Header</label>
                <select name="popup_fb_small_header">
                    <option value="1" <?php echo ('1' == $fb_meta['popup_fb_small_header']) ? 'selected' : ''; ?> >Yes </option>
                    <option value="0" <?php echo ('0'==$fb_meta['popup_fb_small_header'] ) ? 'selected' : ''; ?> >No </option>
                </select>
            </div>



            <div class="form-group"> 
                <label class="" >Show Face      </label>
                <select name="popup_fb_show_face">
                    <option value="1" <?php echo ($fb_meta['popup_fb_show_face'] == 1) ? 'selected' : ''; ?> >Yes </option>
                    <option value="0" <?php echo ($fb_meta['popup_fb_show_face'] == 0) ? 'selected' : ''; ?> >No </option>

                </select>
            </div>

            <div class="form-group"> 
                <label class="" >Hide Call to action button       </label>
                <select name="popup_fb_show_share">
                    <option value="1" <?php echo ($fb_meta['popup_fb_hide_cta'] == 1) ? 'selected' : ''; ?>>Yes </option>
                    <option value="0" <?php echo ($fb_meta['popup_fb_hide_cta'] == 0) ? 'selected' : ''; ?>>No </option>

                </select>
            </div>

        </div>

    </div>

</div>