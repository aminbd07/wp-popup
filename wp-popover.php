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
    public $custom_post_name = 'wpob-modal';
    public $setting_options_name = 'wpobm_global_settings';
    public $global_setting = null;
    public $load_modals = [];
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
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        $settings = get_option($this->setting_options_name);



        $this->global_setting = unserialize($settings);
    }

    function init_actions() {

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_menu', array($this, 'admin_menu'));

        add_action('init', array($this, 'register_post_type'));
        add_shortcode('wpob-modal', array($this, 'render_short_code'));
        add_filter("manage_{$this->custom_post_name}_posts_columns", array($this, 'manage_custom_columns'));
        add_action("manage_{$this->custom_post_name}_posts_custom_column", array($this, 'manage_custom_columns_value'));
        add_action('wp_ajax_wpobm_settings_save', array($this, 'settings_save'));
        add_action('wp_ajax_wpobp_update_theme_save', array($this, 'update_theme_save'));

        //add_action('wp_loaded', array($this, 'add_model_footer'));
        add_filter('the_content', array($this, 'add_model_footer'));
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

    function admin_menu() {
        $capability = 'read'; //minimum level: subscriber 
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('How To USE', $this->text_domain), __('Theme', $this->text_domain), $capability, __('theme', $this->text_domain), array($this, 'manage_submenu_pages'));
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('How To USE', $this->text_domain), __('How to Use', $this->text_domain), $capability, __('how_to_use', $this->text_domain), array($this, 'manage_submenu_pages'));
        add_submenu_page('edit.php?post_type=' . $this->custom_post_name, __('WPOBM Settings', $this->text_domain), __('Setting', $this->text_domain), $capability, __('global_settings', $this->text_domain), array($this, 'manage_submenu_pages'));
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
        $name = "WP Modal";
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
            'show_in_menu' => false,
            'supports' => array('title', 'editor'),
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
        wp_enqueue_script('wpob_modal_backend', plugins_url('/assets/js/admin-script.js', __FILE__), array('wp-color-picker'), false, true);

        if (isset($_GET['page']) && $_GET['page'] == 'theme') {
            wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');

            //Load Theme
            wp_enqueue_style('wpob_modal_backend_style', plugins_url('/assets/css/theme/one.css', __FILE__));
        }
        wp_localize_script('wpob_modal_backend', 'WPOBM_Vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pluginpath' => WPOBM_PATH,
            'pluginurl' => WPOBM_URL,
            'nonce' => wp_create_nonce('wpobm_nonce'),
        ));
    }

    function manage_submenu_pages() {
        $page = $_GET['page'];
        $view_page = 'dehult.php';
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
        $filtered_columns = array_merge($columns, $new_columns);


        return $filtered_columns;
    }

    function manage_custom_columns_value($column) {
        global $post;
        switch ($column) {
            case 'wpobm_sc' :
                echo "[wpob-modal id='{$post->ID}']";
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
            if (!$post) {
                return;
            }
            if ($this->custom_post_name !== $post->post_type) {
                return;
            }
            $title = $post->post_title;
            $content = $post->post_content;
        } else if ($title != '') {
            $title = $title;
            $content = $content;
        }

        ob_start();

        echo $data = "<a href=''  class='' data-toggle='modal' data-target='#wpobm-{$id}'>{$title}</a>";
        ?>


        <div class="modal fade wpobm-modal"   id="wpobm-<?php echo $id ?>"  role="dialog"  >
            <div class="modal-dialog <?php echo $this->global_setting['modal_size'] ?>" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $title ?></h5>
                        <?php if($this->global_setting['show_close_btn'] == 'yes') { ?> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                        <?php } ?>
                    </div>
                    <div class="modal-body">
                        <?php echo $content ?>
                    </div>
                    <?php if($this->global_setting['show_footer'] == 'yes') { ?>
                    <div class="modal-footer">
                        <button type="button" class="btn close-btn" data-dismiss="modal">Close</button>

                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>




        <?php
        //session_start();
        $output = ob_get_clean();

        return $output;
    }

    function add_model_footer($co) {

        return $co;
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

        </style> 
        <?php
    }

}

function WPOBMInit() {
    return WPOBModal::instance();
}

//Class  instance.
$WPOBPopover = WPOBMInit();

