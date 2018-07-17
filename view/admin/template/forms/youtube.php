
<?php
$yt_meta = get_post_meta($post->ID, $this->text_domain . '_yt_settings', TRUE);
$yt_meta = unserialize($yt_meta);

?>

<div>
    <label>Welcome Text before Video  </label>  <br/>

    <textarea placeholder="Popup Body Content" rows="6" cols="80" name="popup_yt"  ><?php echo $popup_data ?></textarea>
</div>


<div id="" class=" postbox popup_type_settings" style="display: block;">
    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Slug</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>YouTube Settings </span></h2>
    <div class="inside">
        <div class="form-group">
            <label class="" >YouTube Video ID </label>
            <input name="popup_yt_vid" type="text" size="13" id="" value="<?php echo $yt_meta['popup_yt_vid'] ?>">
        </div>



        <div class="form-group">
            <label class="" > Width </label>
            <input name="popup_yt_width" type="text" size="13" id="" value="<?php echo $yt_meta['popup_yt_width'] ?>"  class="small">
            <label class="no-fixed"> <input type="radio" value="%" name="popup_yt_wpp" <?php echo ('%' == $yt_meta['popup_yt_wpp']) ? 'checked' : ''; ?>> % </label>
            <label class="no-fixed"> <input type="radio" value="px" name="popup_yt_wpp" <?php echo ('px' == $yt_meta['popup_yt_wpp']) ? 'checked' : ''; ?>> px </label>

        </div>

        <div class="form-group">
            <label class="" > Height </label>
            <input name="popup_yt_height" type="text" size="13" id="" value="<?php echo $yt_meta['popup_yt_height'] ?>"  class="small">px
        </div>


        <div class="form-group">
            <label class="" >Allow Full Screen </label>
            <select name="popup_yt_allowfs">
                <option value="1" <?php echo ('1' == $yt_meta['popup_yt_allowfs']) ? 'selected' : ''; ?> >Yes </option>
                <option value="0" <?php echo ('0'==$yt_meta['popup_yt_allowfs'] ) ? 'selected' : ''; ?> >No </option>

            </select>
        </div>


        <div class="form-group">
            <label class="" >Auto Play   </label>
            <select name="popup_yt_autoplay">
                <option value="1" <?php echo ($yt_meta['popup_yt_autoplay'] == 1) ? 'selected' : ''; ?> >Yes </option>
                <option value="0" <?php echo ($yt_meta['popup_yt_autoplay'] == 0) ? 'selected' : ''; ?> >No </option>

            </select>
        </div>


        <div class="form-group">
            <label class="" > Loop Video </label>
            <select name="popup_yt_videoloop">
                <option value="1" <?php echo ($yt_meta['popup_yt_videoloop'] == 1) ? 'selected' : ''; ?>>Yes </option>
                <option value="0" <?php echo ($yt_meta['popup_yt_videoloop'] == 0) ? 'selected' : ''; ?>>No </option>

            </select>
        </div>


        <div class="form-group">
            <label class="" > Show Video Control </label>
            <select name="popup_yt_videocontorl">
                <option value="1" <?php echo ($yt_meta['popup_yt_videocontorl'] == 1) ? 'selected' : ''; ?>>Yes </option>
                <option value="0" <?php echo ($yt_meta['popup_yt_videocontorl'] == 0) ? 'selected' : ''; ?>>No </option>

            </select>
        </div>


    </div>

</div>

