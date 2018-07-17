<?php
$fb_meta = get_post_meta($post->ID, $this->text_domain . '_fb_settings', TRUE);
$fb_meta = unserialize($fb_meta);
//var_dump($fb_meta);
?>
<div class="modal-body" style="text-align: center">
    <p>
        <?php $this->checkContent($metadata['popup_fb'], 'facebook') ;  ?>
    </p>
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    <?php

    $tabs = unserialize($fb_meta['popup_fb_tab']);
    $tabs = implode(',', $tabs);
    ?>
    <div
        class="fb-page"
        data-href="<?php echo $fb_meta['popup_fb_url'] ?>"
        data-tabs="<?php echo $tabs; ?>"
        data-width="<?php echo $fb_meta['popup_fb_width'] ?>"
        data-height="<?php echo $fb_meta['popup_fb_height'] ?>"
        data-show-facepile="<?php echo $fb_meta['popup_fb_show_face'] == 1 ? TRUE : FALSE ?>"
        data-hide-cover="<?php echo ($fb_meta['popup_fb_hide_cover']==1) ? TRUE : FALSE ; ?>"
        data-small-header="<?php echo ($fb_meta['popup_fb_small_header']==1) ? TRUE : FALSE; ?>"
    >
    </div>

</div>

