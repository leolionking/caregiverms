<?php
namespace CMS;

class Shortcodes {
    public static function init() {
        add_shortcode('cms_caregiver_portal', array(__CLASS__, 'render_caregiver_portal'));
        add_shortcode('cms_login_form', array(__CLASS__, 'render_login_form'));
    }

    public static function render_caregiver_portal() {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to access the caregiver portal.', 'caregiver-management-system') . '</p>';
        }

        $user = wp_get_current_user();
        if (!in_array('caregiver', $user->roles)) {
            return '<p>' . __('Access denied. This portal is for caregivers only.', 'caregiver-management-system') . '</p>';
        }

        ob_start();
        include plugin_dir_path(__FILE__) . '../public/partials/caregiver-portal.php';
        return ob_get_clean();
    }

    public static function render_login_form() {
        if (is_user_logged_in()) {
            return '';
        }

        ob_start();
        include plugin_dir_path(__FILE__) . '../public/partials/login-form.php';
        return ob_get_clean();
    }
}