<?php
/*
 * Plugin Name: Smart Popup
 * Version: 1.0.0
 * Plugin URI:
 * Description: Add popup anywhere in yoursite with short code.  
 * Author: Nurul Amin 
 * Author URI: http://nurul.ninja
 * Requires at least: 4.0
 * Tested up to: 4.9.1
 * License: GPL2
 * Text Domain: wpsp
 * Domain Path: /lang/
 *
 */

class WPSPopup {

    public $version = '1.0.0';
    public $text_domain = 'wpsp';
    public $db_version = '1.0.0';
    public $custom_post_name = 'wps-popup';
    public $setting_options_name = 'wpsp_global_settings';
    public $global_setting = null;
    public $load_popups = [];
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
        add_shortcode('wps-popup', array($this, 'render_short_code'));
        add_filter("manage_{$this->custom_post_name}_posts_columns", array($this, 'manage_custom_columns'));
        add_action("manage_{$this->custom_post_name}_posts_custom_column", array($this, 'manage_custom_columns_value'));
        add_action('wp_ajax_wpsp_settings_save', array($this, 'settings_save'));
        add_action('wp_ajax_wpsp_update_theme_save', array($this, 'update_theme_save'));
        add_action('wp_ajax_wpsp_show_custom_popup_input', array($this, 'show_custom_popup_input'));

        // popup from customizw
        add_action('edit_form_after_title', array($this, 'popup_type_select'));
        add_action('add_meta_boxes_' . $this->custom_post_name, array($this, 'add_fields_meta_box'));
        add_action('save_post', array($this, 'save_post_meta_data'));
        // check shortcode exist in conetn 

        add_action('the_content', array($this, 'add_data_in_post_content'));
    }

    public function define_constants() {
        $this->define('WPSP_VERSION', $this->version);
        $this->define('WPSP_DB_VERSION', $this->db_version);
        $this->define('WPSP_PATH', plugin_dir_path(__FILE__));
        $this->define('WPSP_URL', plugins_url('', __FILE__));
        $this->define('PRO_URL', 'http://wpobserver.com/wp-popup');
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
            'popup_bg_color' => '#000000',
            'popup_bg_opacity' => '100',
            'show_close_btn' => 'yes',
            'top_margin' => '10%',
            'show_footer' => 'yes',
            'popup_size' => 'small'
        );
        $init_data = serialize($init_data);
        update_option($this->setting_options_name, $init_data);
        update_option('wpsp_active_theme', 'one');
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
        wp_enqueue_script('wps_popup_front', plugins_url('assets/js/script.js', __FILE__), '', false, true);
        wp_enqueue_style('wps_popup_front', plugins_url('/assets/css/style.css', __FILE__));

        $act_theme = get_option('wpsp_active_theme');
        wp_enqueue_style('wps_popup_front_theme', plugins_url('/assets/css/theme/' . $act_theme . '.css', __FILE__));
    }

    function admin_enqueue() {

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('wps_popup_backend', plugins_url('/assets/css/admin_style.css', __FILE__));
        wp_enqueue_script('wps_popup_backend', plugins_url('/assets/js/admin-script.js', __FILE__), array('wp-color-picker', 'jquery'), false, false);
        if (isset($_GET['page']) && $_GET['page'] == 'theme') {
            wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
            //Load Theme
            $act_theme = get_option('wpsp_active_theme');
            wp_enqueue_style('wps_popup_backend_style', plugins_url('/assets/css/theme/' . $act_theme . '.css', __FILE__));
        }

        wp_localize_script('wps_popup_backend', 'WPSP_Vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pluginpath' => WPSP_PATH,
            'pluginurl' => WPSP_URL,
            'nonce' => wp_create_nonce('wpsp_nonce'),
        ));
    }

    function admin_menu() {
        $capability = 'read'; //minimum level: subscriber 
        //add_menu_page( 'WP Popup', 'WP Popup', $capability, $this->custom_post_name, array( $this, 'manage_menu_pages' ) , 'dashicons-feedback', 6 );

        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('Theme', $this->text_domain), __('Theme', $this->text_domain), $capability, __('theme', $this->text_domain), array($this, 'manage_menu_pages'));
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('How To USE', $this->text_domain), __('How to Use', $this->text_domain), $capability, __('how_to_use', $this->text_domain), array($this, 'manage_menu_pages'));
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('WPSP Settings', $this->text_domain), __('Setting', $this->text_domain), $capability, __('global_settings', $this->text_domain), array($this, 'manage_menu_pages'));
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
        require ( WPSP_PATH . '/view/' . $view_page );
    }

    function manage_custom_columns($columns) {

        $new_columns['wpsp_sc'] = "Short Code";
        $new_columns['wpsp_type'] = "Popup Type";
        $filtered_columns = array_merge($columns, $new_columns);


        return $filtered_columns;
    }

    function manage_custom_columns_value($column) {
        global $post;

        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);

        switch ($column) {
            case 'wpsp_sc' :
                echo "[wps-popup id='{$post->ID}']";
                break;
            case 'wpsp_type' :
                echo $metadata['popup_type'];
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
        //check_ajax_referer('wpsp_nonce', $post['nonce']);
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
        //check_ajax_referer('wpsp_nonce', $post['nonce']);
        parse_str($post['form_data'], $form_data);

        //$form_data = serialize($form_data);

        update_option('wpsp_active_theme', $form_data['wpsp_active_theme']);

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
            echo $data = "<a href='' {$style}  class=' {$class}' data-toggle='modal' data-target='#wpsp-{$id}'>{$title}</a>";
        }

        //include WPSP_PATH . '/view/frontend/popup.php';

        $output = ob_get_clean();

        return $output;
    }

    function add_data_in_post_content($content) {

        $pattern = get_shortcode_regex(array('wps-popup'));
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
            $html .= $this->get_popup_data($id);
        }
        return $content . $html;
    }

    function get_popup_data($id) {
        $post = get_post($id);

        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);

        ob_start();
        $popup_type = $metadata['popup_type'];

        include WPSP_PATH . '/view/frontend/popup.php';

        $output = ob_get_clean();

        return $output;
    }

    function popup_type_select($post) {

        if ($post->post_type != $this->custom_post_name) {
            return;
        }

        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);

        $metadata = unserialize($metadata);


        include WPSP_PATH . '/view/admin/template/popup_type_select.php';
    }

    function checkContent($content, $type) {
        switch ($type) {

            case "image" :
                $content = file_exists($content) ? $content : "Image not found";
                break;

            case "facebook" :
            case "html":
                $content = strip_shortcodes($content);
                $content = $this->conetnt_filter($content);

                break;

            case "shortcode" :
                $content = $content;
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
        if ($this->is_pro()) {
            return $content;
        }
        // match any iframes
        $pattern = '~<iframe.*</iframe>|<embed.*</embed>|<object.*</object>~';
        preg_match_all($pattern, $content, $matches);
        foreach ($matches as $key => $value) {
            if (empty($value)) {
                unset($matches[$key]);
            }
        }

        if (!empty($matches))
            $content = "Iframe and any video tags are not allowed! use specific type for content";

        return $content;
    }

    function show_custom_popup_input() {

        global $wp;
        $type = $_POST['popuptype'];
        $post = get_post($_POST['post_ID']);

        $page = '';
        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);
        // $popup_data = $post->post_excerpt;

        switch ($type) {

            case "image" :
                $popup_data = $metadata['popup_image'];
                $page = 'image.php';
                break;

            case "facebook" :
                $popup_data = $metadata['popup_fb'];
                $page = 'facebook.php';

                break;

            case "shortcode" :
                $popup_data = $metadata['popup_sc'];
                $page = 'shortcode.php';
                break;
            case "youtube" :
                $popup_data = $metadata['popup_yt'];
                $page = 'youtube.php';
                break;

            default:
                $page = 'pro.php';
        }



        include WPSP_PATH . 'view/admin/template/forms/' . $page;

        die();
    }

    function add_fields_meta_box($post) {
        add_meta_box($this->custom_post_name . '_meta_box', __('Popup Settings', $this->text_domain), array($this, 'wp_popup_custom_post_settings'), 'wps-popup', 'side', 'low');
        // add_meta_box($this->custom_post_name . '_meta_box2', __('Popup Advance Settings', $this->text_domain), array($this, 'wp_popup_custom_post_advance_settings'), 'wps-popup', 'normal', 'low');
    }

    function wp_popup_custom_post_settings() {
        global $post;
        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);
        include WPSP_PATH . 'view/admin/template/forms/popup-settings.php';
    }

    function wp_popup_custom_post_advance_settings() {
        global $post;
        $metadata = get_post_meta($post->ID, $this->text_domain . '_popup_settings', TRUE);
        $metadata = unserialize($metadata);

        include WPSP_PATH . 'pro/view/popup-advance-settings.php';
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
                $popup_image = (isset($post['popup_image']) ) ? sanitize_text_field($post['popup_image']) : '';
                $popup_fb = (isset($post['popup_fb']) ) ? sanitize_text_field($post['popup_fb']) : '';
                $popup_yt = (isset($post['popup_yt']) ) ? sanitize_text_field($post['popup_yt']) : '';
                $popup_sc = (isset($post['popup_sc']) ) ? sanitize_text_field($post['popup_sc']) : '';

                $data = array(
                    'popup_type' => $popup_type,
                    'load_on' => $load_on,
                    'show_as' => $show_as,
                    'button_color' => $button_color,
                    'button_border_radious' => $button_border_radious,
                    'load_after' => $load_after,
                    'popup_image' => $popup_image,
                    'popup_fb' => $popup_fb,
                    'popup_yt' => $popup_yt,
                    'popup_sc' => stripslashes($popup_sc),
                );
                update_post_meta($post_ID, $this->text_domain . '_popup_settings', serialize($data));

                if ($popup_type == 'facebook') {
                    $popup_fb_url = isset($post['popup_fb_url']) ? sanitize_text_field($post['popup_fb_url']) : 'https://www.facebook.com/bitbytetech/';
                    $popup_fb_width = isset($post['popup_fb_width']) ? sanitize_text_field($post['popup_fb_width']) : '200';
                    $popup_fb_height = isset($post['popup_fb_height']) ? sanitize_text_field($post['popup_fb_height']) : '500';
                    $popup_fb_tab = isset($post['popup_fb_tab']) ? serialize($post['popup_fb_tab']) : [];
                    $popup_fb_hide_cover = isset($post['popup_fb_hide_cover']) ? sanitize_text_field($post['popup_fb_hide_cover']) : '0';
                    $popup_fb_small_header = isset($post['popup_fb_small_header']) ? sanitize_text_field($post['popup_fb_small_header']) : '0';
                    $popup_fb_show_face = isset($post['popup_fb_show_face']) ? sanitize_text_field($post['popup_fb_show_face']) : '1';
                    $popup_fb_hide_cta = isset($post['popup_fb_hide_cta']) ? sanitize_text_field($post['popup_fb_hide_cta']) : '0';

                    $fb_data = array(
                        'popup_fb_url' => $popup_fb_url,
                        'popup_fb_width' => $popup_fb_width,
                        'popup_fb_height' => $popup_fb_height,
                        'popup_fb_tab' => $popup_fb_tab,
                        'popup_fb_hide_cover' => $popup_fb_hide_cover,
                        'popup_fb_small_header' => $popup_fb_small_header,
                        'popup_fb_show_face' => $popup_fb_show_face,
                        'popup_fb_hide_cta' => $popup_fb_hide_cta
                    );
                    update_post_meta($post_ID, $this->text_domain . '_fb_settings', serialize($fb_data));
                }
                if ($popup_type == 'youtube') {
                    $popup_yt_vid = isset($post['popup_yt_vid']) ? sanitize_text_field($post['popup_yt_vid']) : '';
                    $popup_yt_width = isset($post['popup_yt_width']) ? sanitize_text_field($post['popup_yt_width']) : '500';
                    $popup_yt_wpp = isset($post['popup_yt_wpp']) ? sanitize_text_field($post['popup_yt_wpp']) : 'px';
                    $popup_yt_height = isset($post['popup_yt_height']) ? sanitize_text_field($post['popup_yt_height']) : '450';
                    $popup_yt_allowfs = isset($post['popup_yt_allowfs']) ? sanitize_text_field($post['popup_yt_allowfs']) : 1;
                    $popup_yt_autoplay = isset($post['popup_yt_autoplay']) ? sanitize_text_field($post['popup_yt_autoplay']) : '0';
                    $popup_yt_videoloop = isset($post['popup_yt_videoloop']) ? sanitize_text_field($post['popup_yt_videoloop']) : '0';
                    $popup_yt_videocontorl = isset($post['popup_yt_videocontorl']) ? sanitize_text_field($post['popup_yt_videocontorl']) : '1';


                    $yt_data = array(
                        'popup_yt_vid' => $popup_yt_vid,
                        'popup_yt_width' => $popup_yt_width,
                        'popup_yt_wpp' => $popup_yt_wpp,
                        'popup_yt_height' => $popup_yt_height,
                        'popup_yt_allowfs' => $popup_yt_allowfs,
                        'popup_yt_autoplay' => $popup_yt_autoplay,
                        'popup_yt_videoloop' => $popup_yt_videoloop,
                        'popup_yt_videocontorl' => $popup_yt_videocontorl,
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
                background-color: <?php echo $this->global_setting['popup_bg_color'] ?>;
                opacity: <?php echo ($this->global_setting['popup_bg_opacity'] / 100 ) ?> !important ;

            }

            .custom_size{
                max-width:<?php echo $this->global_setting['popup_custom_width'] ?>; 
            }

        </style> 
        <?php
    }

    function is_pro() {
        return false;
    }

}

function WPSPInit() {
    return WPSPopup::instance();
}

//Class  instance.
$WPSPopover = WPSPInit();

