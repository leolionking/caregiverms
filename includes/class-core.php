<?php
namespace CMS;

class Core {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = CMS_VERSION;
        $this->plugin_name = 'caregiver-management-system';
        
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register_shortcodes();
        $this->initialize_ajax_handlers();
    }

    private function load_dependencies() {
        require_once CMS_PLUGIN_DIR . 'includes/class-loader.php';
        require_once CMS_PLUGIN_DIR . 'includes/class-i18n.php';
        require_once CMS_PLUGIN_DIR . 'admin/class-admin.php';
        require_once CMS_PLUGIN_DIR . 'admin/class-ajax-handler.php';
        require_once CMS_PLUGIN_DIR . 'public/class-public.php';
        require_once CMS_PLUGIN_DIR . 'includes/models/class-locations.php';
        require_once CMS_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once CMS_PLUGIN_DIR . 'includes/ajax/class-caregiver-ajax.php';
        require_once CMS_PLUGIN_DIR . 'admin/class-ajax-schedules.php';

        $this->loader = new Loader();
    }

    private function set_locale() {
        $plugin_i18n = new I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {
        $plugin_admin = new Admin\Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        // Initialize AJAX handlers
        Admin\Ajax_Handler::init();
        
        Admin\Ajax_Schedules::init();
    }

    private function define_public_hooks() {
        $plugin_public = new Frontend\Public_Frontend($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    private function register_shortcodes() {
        Shortcodes::init();
    }

    private function initialize_ajax_handlers() {
        Ajax\Caregiver_Ajax::init();
    }
    
    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
}