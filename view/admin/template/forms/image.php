

<label for="upload_image">
    <input id="upload_image" type="text" size="36" name="popup_image" value="<?php echo $popup_data; ?>" /> 
    <input id="upload_image_button" class="button" type="button" value="Image from Gallery" />
    <br />Enter a URL or upload an image <br/>
    <?php
    if ($popup_data != "") {
       echo "Current Image <br/>";
       echo "<img src='".$popup_data ."' width='150' />"; 
    }
    ?>
</label>

<script>
    jQuery(document).ready(function ($) {


        var custom_uploader;


        $('#upload_image_button').click(function (e) {

            e.preventDefault();

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function () {
                console.log(custom_uploader.state().get('selection').toJSON());
                attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#upload_image').val(attachment.url);
            });

            //Open the uploader dialog
            custom_uploader.open();

        });


    });
</script>
