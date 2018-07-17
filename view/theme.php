<div class="<?php echo $this->text_domain ?> how-to-use"> 
    <h1>popup Theme</h1>
    <div class="row"> 
        <div class="col-md-5"> 
            <form method="post" id='wpsp-theme'>
                <?php 
                 $act_theme = get_option('wpsp_active_theme'); 
                ?>
                <div class="form-group">
                    <label> Select a Theme   </label>
                    <select name="wpsp_active_theme" class="form-control theme-change ">
                        <option value="one" <?php echo $act_theme == 'one'? 'selected' : '' ?> >Theme 1</option>
                        <option value="tow" <?php echo $act_theme == 'tow'? 'selected' : '' ?>  >Theme 2</option>
                        <option value="three" <?php echo $act_theme == 'three'? 'selected' : '' ?>>Theme 3</option>
                        <option value="four" <?php echo $act_theme == 'four'? 'selected' : '' ?>>Theme 4</option>
                    </select>
                </div>
                <div>
                    <input type="submit" name="submit" value="Update" class="btn btn-warning" />
                </div>
                <div class="update-status" style="display: none">
                </div>
            </form>
            <br/>
            <br/>
            <br/>
            <div>
                <label> <input type="checkbox" name="custom_theme" class="customize_theme" value="1" /> Use Custom Design Theme </label>
                
                <div class="customize_theme_area hide">
                    <p class="coming-soon">  Coming soon ...  </p>
                </div>
                
            </div>
        </div>
        <div class="col-md-7 preview-area">

            <h3 class="text-center">Preview </h3>
            <div class="theme-preview-continer ">     
                <div class="modal" id="theme-preview" tabindex="-1" role="dialog" style="display: block">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="modal-title">popup title</div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"></span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <p>popup body text goes here.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn close-btn" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>