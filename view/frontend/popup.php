<?php

?>
<div class="modal fade wpsp-popup"   id="wpsp-<?php echo $id ?>"  role="dialog"  >
    <div class="modal-dialog <?php echo $this->global_setting['popup_size'] ?>" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $post->post_title ?></h5>
                <?php if ($this->global_setting['show_close_btn'] == 'yes') { ?> 
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                <?php } ?>
            </div>
           
                
                <?php 
                include WPSP_PATH . '/view/frontend/popup-type/'.$metadata['popup_type'].'.php';
                ?>
            
            <?php if ($this->global_setting['show_footer'] == 'yes') { ?>
                <div class="modal-footer">
                    <button type="button" class="btn close-btn" data-dismiss="modal">Close</button>

                </div>
            <?php } ?>
        </div>
    </div>
    <?php
$delay = ( $metadata['load_after'] * 1000 );
if ($metadata['load_on'] == 'onload') {
    ?>
    <script type="text/javascript">
        jQuery(window).on('load', function () {
            setTimeout(function () {
                jQuery('#wpsp-<?php echo $id ?>').modal('show');
            }, <?php echo $delay; ?>);
        });
    </script>
<?php } ?>
</div>
