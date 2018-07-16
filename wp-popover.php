<?php
/*
 * Plugin Name: WP Modal
 * Version: 1.0.0
 * Plugin URI:
 * Description: Add Modal anywhere in yoursite with short code.  
 * Author: Nurul Amin 
 * Author URI: http://nurul.ninja
 * Requires at least: 4.0
 * Tested up to: 4.9.1
 * License: GPL2
 * Text Domain: wpobm  
 * Domain Path: /lang/
 *
 */

class WPOBModal {

    public $version = '1.0.0';
    public $text_domain = 'wpobm';
    public $db_version = '1.0.0';
    public $custom_post_name = 'wpob-popup';
    public $setting_options_name = 'wpobm_global_settings';
    public $global_setting = null;
    public $load_modals = [];
    public $custom_fields = [];
    protected static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->init_actions();
        $this->define_constants();
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        add_action('wp_head', array($this, 'render_global_style'));


        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        $settings = get_option($this->setting_options_name);
        $this->global_setting = unserialize($settings);

        $this->custom_fields = array(
            array(
                'label' => __('Open On Page Load', $this->text_domain),
                'input' => 'html',
                'value' => '<select> <option> Yes </option><option> No</option> </select>',
            ),
            array(
                'label' => __('Clickable Button in Post', $this->text_domain),
                'input' => 'html',
                'value' => '<select> <option> Yes </option><option> No</option> </select>',
            ),
        );
    }

    function init_actions() {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_menu', array($this, 'admin_menu'));

        add_action('init', array($this, 'register_post_type'));
        add_shortcode('wpob-popup', array($this, 'render_short_code'));
        add_filter("manage_{$this->custom_post_name}_posts_columns", array($this, 'manage_custom_columns'));
        add_action("manage_{$this->custom_post_name}_posts_custom_column", array($this, 'manage_custom_columns_value'));
        add_action('wp_ajax_wpobm_settings_save', array($this, 'settings_save'));
        add_action('wp_ajax_wpobp_update_theme_save', array($this, 'update_theme_save'));
        add_action('wp_ajax_wpobp_show_custom_popup_input', array($this, 'show_custom_popup_input'));

        // popup from customizw
        add_action('edit_form_after_title', array($this, 'popup_type_select'));
        add_action('add_meta_boxes_' . $this->custom_post_name, array($this, 'add_fields_meta_box'));
        add_action('save_post', array($this, 'save_post_meta_data'));
        // check shortcode exist in conetn 

        add_action('the_content', array($this, 'add_data_in_post_content'));
    }

    public function define_constants() {
        $this->define('WPOBM_VERSION', $this->version);
        $this->define('WPOBM_DB_VERSION', $this->db_version);
        $this->define('WPOBM_PATH', plugin_dir_path(__FILE__));
        $this->define('WPOBM_URL', plugins_url('', __FILE__));
    }

    public function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    function load_textdomain() {
        load_plugin_textdomain($this->text_domain, false, dirname(plugin_basename(__FILE__)) . '/lang/');
    }

    public function activate() {
        flush_rewrite_rules();
        $init_data = array(
            'modal_bg_color' => '#000000',
            'modal_bg_opacity' => '100',
            'show_close_btn' => 'yes',
            'top_margin' => '10%',
            'show_footer' => 'yes',
            'modal_size' => 'small'
        );
        $init_data = serialize($init_data);
        update_option($this->setting_options_name, $init_data);
        update_option('wpobm_active_theme', 'one');
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function uninstall() {
        
    }

    function register_post_type() {
        $name = "WP Popup";
        $labels = array(
            'name' => __($name, 'post type general name', $this->text_domain),
            'singular_name' => __($name, 'post type singular name', $this->text_domain),
            'add_new' => __('Add New', $name, $this->text_domain),
            'add_new_item' => __('Add New ' . $name, $this->text_domain),
            'edit_item' => __('Edit ' . $name, $this->text_domain),
            'new_item' => __('New ' . $name, $this->text_domain),
            'view_item' => __('View ' . $name, $this->text_domain),
            'search_items' => __('Search ' . $name, $this->text_domain),
            'not_found' => __('Nothing found', $this->text_domain),
            'not_found_in_trash' => __('Nothing found in Trash', $this->text_domain),
            'parent_item_colon' => __($name, $this->text_domain),
        );
        $post_type_agr = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'menu_position' => false,
            'show_in_menu' => true,
            'publicly_queryable' => FALSE,
            'supports' => array('title', 'editor', 'revisions'),
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
            'show_in_nav_menus' => false,
        );
        register_post_type($this->custom_post_name, $post_type_agr);
    }

    function enqueue() {

        wp_enqueue_script('jquery');
        wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
        wp_enqueue_script('wpob_modal_front', plugins_url('assets/js/script.js', __FILE__), '', false, true);
        wp_enqueue_style('wpob_modal_front', plugins_url('/assets/css/style.css', __FILE__));

        $act_theme = get_option('wpobm_active_theme');
        wp_enqueue_style('wpob_modal_front_theme', plugins_url('/assets/css/theme/' . $act_theme . '.css', __FILE__));
    }

    function admin_enqueue() {

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('wpob_modal_backend', plugins_url('/assets/css/admin_style.css', __FILE__));
        wp_enqueue_script('wpob_modal_backend', plugins_url('/assets/js/admin-script.js', __FILE__), array('wp-color-picker', 'jquery'), false, false);
        if (isset($_GET['page']) && $_GET['page'] == 'theme') {
            wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
            //Load Theme
            $act_theme = get_option('wpobm_active_theme');
            wp_enqueue_style('wpob_modal_backend_style', plugins_url('/assets/css/theme/' . $act_theme . '.css', __FILE__));
        }

        wp_localize_script('wpob_modal_backend', 'WPOBM_Vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pluginpath' => WPOBM_PATH,
            'pluginurl' => WPOBM_URL,
            'nonce' => wp_create_nonce('wpobm_nonce'),
        ));
    }

    function admin_menu() {
        $capability = 'read'; //minimum level: subscriber 
        //add_menu_page( 'WP Popup', 'WP Popup', $capability, $this->custom_post_name, array( $this, 'manage_menu_pages' ) , 'dashicons-feedback', 6 );

        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('Theme', $this->text_domain), __('Theme', $this->text_domain), $capability, __('theme', $this->text_domain), array($this, 'manage_menu_pages'));
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('How To USE', $this->text_domain), __('How to Use', $this->text_domain), $capability, __('how_to_use', $this->text_domain), array($this, 'manage_menu_pages'));
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('WPOBM Settings', $this->text_domain), __('Setting', $this->text_domain), $capability, __('global_settings', $this->text_domain), array($this, 'manage_menu_pages'));
    }

    function manage_menu_pages() {

        $page = $_GET['page'];
        $sub_page = isset($_GET['sub']) ? $_GET['sub'] : '';

        $view_page = 'home.php';
        switch ($page) {

            case "how_to_use" :
                $view_page = "how_to_use.php";
                break;

            case "theme" :
                $view_page = "theme.php";
                break;

            case "global_settings" :
                $view_page = "settings.php";
                break;

            default:
        }
        require ( WPOBM_PATH . '/view/' . $view_page );
    }

    function manage_custom_columns($columns) {

        $new_columns['wpobm_sc'] = "Short Code";
        $new_columns['wpobm_type'] = "Popup Type";
        $filtered_columns = array_merge($columns, $new_columns);


        return $filtered_columns;
    }

    function manage_custom_columns_value($column) {
        global $post;
        switch ($column) {
            case 'wpobm_sc' :
                echo "[wpob-popup id='{$post->ID}']";
                break;
            case 'wpobm_type' :
                echo "Post Type";
                break;

            default :
                break;
        }
    }

    /**
     * Save settings
     */
    function settings_save() {
        $post = $_POST;
        //check_ajax_referer('wpobm_nonce', $post['nonce']);
        parse_str($post['form_data'], $form_data);

        $form_data = serialize($form_data);

        update_option($this->setting_options_name, $form_data);

        echo "Save Success!!";

        die();
    }

    /**
     * Update Theme
     */
    function update_theme_save() {
        $post = $_POST;
        //check_ajax_referer('wpobm_nonce', $post['nonce']);
        parse_str($post['form_data'], $form_data);

        //$form_data = serialize($form_data);

        update_option('wpobm_active_theme', $form_data['wpobm_active_theme']);

        echo "Theme Update  Success!!";

        die();
    }

    function render_short_code($atts, $content = null) {

        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        $a = shortcode_atts(array(
            'id' => '',
            'title' => '',
                ), $atts);

        extract($a);


        if ($id) {
            $post = get_post((int) $id);
            $title = $post->post_title;
        }

        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);

        ob_start();
        $popup_type = $metadata['popup_type'];

        if ($metadata['load_on'] == 'onclick') {
            $class = $metadata['show_as'] == 'button' ? 'btn btn-primary' : '';
            $style = "style=' background-color:{$metadata['button_color']};  border-radius:{$metadata['button_border_radious']}px; border-color:{$metadata['button_color']}'";
            $style = $metadata['show_as'] == 'button' ? $style : '';
            echo $data = "<a href='' {$style}  class=' {$class}' data-toggle='modal' data-target='#wpobm-{$id}'>{$title}</a>";
        }

        //include WPOBM_PATH . '/view/frontend/modal.php';

        $output = ob_get_clean();

        return $output;
    }

    function add_data_in_post_content($content) {

        $pattern = get_shortcode_regex(array('wpob-popup'));
        preg_match_all('/' . $pattern . '/s', $content, $matches);

        $scs = $matches[0];
        $keys = array();
        $result = array();
        foreach ($scs as $key => $val) {

            $get = str_replace(" ", "&", $scs[$key]);
            parse_str($get, $output);
            array_push($result, $output);
        }
        $html = "";
        foreach ($result as $key => $val) {
            $id = $val['id'];
            $id = str_replace('"', '', $id);
            $id = str_replace("'", '', $id);
            $id = intval($id);
            $html .= $this->get_modal_data($id);
        }
        return $content . $html;
    }

    function get_modal_data($id) {
        $post = get_post($id);

        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);

        ob_start();
        $popup_type = $metadata['popup_type'];

        include WPOBM_PATH . '/view/frontend/modal.php';

        $output = ob_get_clean();

        return $output;
    }

    function popup_type_select($post) {

        if ($post->post_type != $this->custom_post_name) {
            return;
        }

        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);

        $metadata = unserialize($metadata);


        include WPOBM_PATH . '/view/admin/template/popup_type_select.php';
    }

    function checkContent($content, $type){
        switch ($type){

            case "image" :
                  $content = file_exists($content) ? $content : "Image not found" ;
                break;

            case "facebook" :
            case "html":
             $content = strip_shortcodes($content);
             $content = $this->conetnt_filter($content);

                break;

            case "shortcode" :
                $content = $content ;
                break;
            case "youtube" :
                $content = $content; 
                break;

            default:
                $content = $content;
        }
        echo $content;
    }

    function conetnt_filter($content) {
        // match any iframes
        $pattern = '~<iframe.*</iframe>|<embed.*</embed>|<object.*</object>~';
        preg_match_all($pattern, $content, $matches);
        if(!empty($matches)) $content = "Iframe and any video tags are not allowed! use specific type for content";

        return $content;
    }


    function show_custom_popup_input() {

        global $wp;
        $type = $_POST['popuptype'];
        $post = get_post($_POST['post_ID']);
            
        $page = '';
        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);
        // $modal_data = $post->post_excerpt;

        switch ($type) {

            case "image" :
                $modal_data = $metadata['modal_image'];
                $page = 'image.php';
                break;

            case "facebook" :
                $modal_data = $metadata['modal_fb'];
                $page = 'facebook.php';
            
                break;

            case "shortcode" :
                $modal_data = $metadata['modal_sc'];
                $page = 'shortcode.php';
                break;
            case "youtube" :
                $modal_data = $metadata['modal_yt'];
                $page = 'youtube.php';
                break;

            default:
                $page = 'pro.php';
        }



        include WPOBM_PATH . 'view/admin/template/forms/' . $page;

        die();
    }

    function add_fields_meta_box($post) {
        add_meta_box($this->custom_post_name . '_meta_box', __('Popup Settings', $this->text_domain), array($this, 'wp_popup_custom_post_settings'), 'wpob-popup', 'side', 'low');
    }

    function wp_popup_custom_post_settings() {
        global $post;
        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);
        include WPOBM_PATH . 'view/admin/template/forms/popup-settings.php';
    }

    function save_post_meta_data() {
        if ($_POST) {
            $post = $_POST;

            if ($post['post_type'] == $this->custom_post_name) {
                $post_ID = $post['ID'];
                $popup_type = (isset($post['popup_type']) ) ? sanitize_text_field($post['popup_type']) : 'html';
                $load_on = (isset($post['load_on']) ) ? sanitize_text_field($post['load_on']) : 'onclick';
                $show_as = (isset($post['show_as']) ) ? sanitize_text_field($post['show_as']) : 'link';
                $button_color = (isset($post['button_color']) ) ? sanitize_text_field($post['button_color']) : '#cccccc';
                $button_border_radious = (isset($post['button_border_radious']) ) ? sanitize_text_field($post['button_border_radious']) : '0';
                $load_after = (isset($post['load_after']) ) ? sanitize_text_field($post['load_after']) : '0';
                $modal_image = (isset($post['modal_image']) ) ? sanitize_text_field($post['modal_image']) : '';
                $modal_fb = (isset($post['modal_fb']) ) ? sanitize_text_field($post['modal_fb']) : '';
                $modal_yt = (isset($post['modal_yt']) ) ? sanitize_text_field($post['modal_yt']) : '';
                $modal_sc = (isset($post['modal_sc']) ) ? sanitize_text_field($post['modal_sc']) : '';

                $data = array(
                    'popup_type' => $popup_type,
                    'load_on' => $load_on,
                    'show_as' => $show_as,
                    'button_color' => $button_color,
                    'button_border_radious' => $button_border_radious,
                    'load_after' => $load_after,
                    'modal_image' => $modal_image,
                    'modal_fb' => $modal_fb,
                    'modal_yt' => $modal_yt,
                    'modal_sc' => stripslashes($modal_sc),
                );
                update_post_meta($post_ID, $this->text_domain . '_popup_settings', serialize($data));

                if ($popup_type == 'facebook') {
                    $modal_fb_url = isset($post['modal_fb_url']) ? sanitize_text_field($post['modal_fb_url']) : 'https://www.facebook.com/bitbytetech/';
                    $modal_fb_width = isset($post['modal_fb_width']) ? sanitize_text_field($post['modal_fb_width']) : '200';
                    $modal_fb_height = isset($post['modal_fb_height']) ? sanitize_text_field($post['modal_fb_height']) : '500';
                    $modal_fb_tab = isset($post['modal_fb_tab']) ? serialize($post['modal_fb_tab']) : [];
                    $modal_fb_hide_cover = isset($post['modal_fb_hide_cover']) ? sanitize_text_field($post['modal_fb_hide_cover']) : '0';
                    $modal_fb_small_header = isset($post['modal_fb_small_header']) ? sanitize_text_field($post['modal_fb_small_header']) : '0';
                    $modal_fb_show_face = isset($post['modal_fb_show_face']) ? sanitize_text_field($post['modal_fb_show_face']) : '1';
                    $modal_fb_hide_cta = isset($post['modal_fb_hide_cta']) ? sanitize_text_field($post['modal_fb_hide_cta']) : '0';

                    $fb_data = array(
                        'modal_fb_url' => $modal_fb_url,
                        'modal_fb_width' => $modal_fb_width,
                        'modal_fb_height' => $modal_fb_height,
                        'modal_fb_tab' => $modal_fb_tab,
                        'modal_fb_hide_cover' => $modal_fb_hide_cover,
                        'modal_fb_small_header' => $modal_fb_small_header,
                        'modal_fb_show_face' => $modal_fb_show_face,
                        'modal_fb_hide_cta' => $modal_fb_hide_cta
                    );
                    update_post_meta($post_ID, $this->text_domain . '_fb_settings', serialize($fb_data));
                }
                if ($popup_type == 'youtube') {
                    $modal_yt_vid = isset($post['modal_yt_vid']) ? sanitize_text_field($post['modal_yt_vid']) : '';
                    $modal_yt_width = isset($post['modal_yt_width']) ? sanitize_text_field($post['modal_yt_width']) : '500';
                    $modal_yt_wpp = isset($post['modal_yt_wpp']) ? sanitize_text_field($post['modal_yt_wpp']) : 'px';
                    $modal_yt_height = isset($post['modal_yt_height']) ? sanitize_text_field($post['modal_yt_height']) : '450';
                    $modal_yt_allowfs = isset($post['modal_yt_allowfs']) ? sanitize_text_field($post['modal_yt_allowfs']) : 1;
                    $modal_yt_autoplay = isset($post['modal_yt_autoplay']) ? sanitize_text_field($post['modal_yt_autoplay']) : '0';
                    $modal_yt_videoloop = isset($post['modal_yt_videoloop']) ? sanitize_text_field($post['modal_yt_videoloop']) : '0';
                    $modal_yt_videocontorl = isset($post['modal_yt_videocontorl']) ? sanitize_text_field($post['modal_yt_videocontorl']) : '1';


                    $yt_data = array(
                        'modal_yt_vid' => $modal_yt_vid,
                        'modal_yt_width' => $modal_yt_width,
                        'modal_yt_wpp' => $modal_yt_wpp,
                        'modal_yt_height' => $modal_yt_height,
                        'modal_yt_allowfs' => $modal_yt_allowfs,
                        'modal_yt_autoplay' => $modal_yt_autoplay,
                        'modal_yt_videoloop' => $modal_yt_videoloop,
                        'modal_yt_videocontorl' => $modal_yt_videocontorl,

                    );
                    update_post_meta($post_ID, $this->text_domain . '_yt_settings', serialize($yt_data));
                }


            }
        }
    }

    function render_global_style() {
        ?> 
        <style>
            .modal.show .modal-dialog{
                top: <?php echo $this->global_setting['top_margin'] ?>;

            }

            .modal-backdrop {
                background-color: <?php echo $this->global_setting['modal_bg_color'] ?>;
                opacity: <?php echo ($this->global_setting['modal_bg_opacity'] / 100 ) ?> !important ;

            }

            .custom_size{
                max-width:<?php echo $this->global_setting['modal_custom_width'] ?>; 
            }

        </style> 
        <?php
    }

}

function WPOBMInit() {
    return WPOBModal::instance();
}

//Class  instance.
$WPOBPopover = WPOBMInit();

