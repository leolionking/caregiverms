<?php
namespace CMS\Frontend;

class Public_Frontend {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/public.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/public.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'cmsPublic', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cms_public_nonce'),
            'loginRedirect' => home_url()
        ));
    }

    public function register_shortcodes() {
        add_shortcode('cms_caregiver_portal', array($this, 'render_caregiver_portal'));
    }

    public function render_caregiver_portal() {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to access the caregiver portal.', 'caregiver-management-system') . '</p>';
        }

        $user = wp_get_current_user();
        if (!in_array('caregiver', $user->roles)) {
            return '<p>' . __('Access denied. This portal is for caregivers only.', 'caregiver-management-system') . '</p>';
        }

        ob_start();
        include 'partials/caregiver-portal.php';
        return ob_get_clean();
    }
}