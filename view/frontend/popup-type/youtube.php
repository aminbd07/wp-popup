
<?php
$yt_meta = get_post_meta($post->ID, $this->text_domain . '_yt_settings', TRUE);
$yt_meta = unserialize($yt_meta);
//var_dump($fb_meta);
$videoloop = ($yt_meta['popup_yt_videoloop']) == 1 ? "&playlist=". $yt_meta['popup_yt_vid'] . "&loop=" . $yt_meta['popup_yt_videoloop']  : "&loop=" . $yt_meta['popup_yt_videoloop']  ;
?>

<div class="modal-body" style="text-align: center">
    <div style="text-align: left"><?php $this->checkContent($metadata['popup_yt'], 'youtube') ;  ?> </div>
    <iframe id="ytplayer" type="text/html" width="<?php echo $yt_meta['popup_yt_width'] ; ?><?php echo $yt_meta['popup_yt_wpp'] ; ?>" height="<?php echo $yt_meta['popup_yt_height'] ; ?>"
            src="https://www.youtube.com/embed/<?php echo $yt_meta['popup_yt_vid'] ; ?>?autoplay=<?php echo $yt_meta['popup_yt_autoplay'] ; ?>&controls=<?php echo $yt_meta['popup_yt_videocontorl'] ; ?>&fs=<?php echo $yt_meta['popup_yt_allowfs'] ; ?>&rel=0<?php echo $videoloop;?>"
            frameborder="0"></iframe>


</div>

