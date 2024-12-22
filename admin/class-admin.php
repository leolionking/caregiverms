<?php
namespace CMS\Admin;

class Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        // Enqueue admin.js
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Enqueue location-handler.js
        wp_enqueue_script(
            $this->plugin_name . '-location',
            plugin_dir_url(__FILE__) . 'js/location-handler.js',
            array('jquery'),
            $this->version,
            true
        );

        // Enqueue schedules.js
        wp_enqueue_script(
            $this->plugin_name . '-schedule',
            plugin_dir_url(__FILE__) . 'js/schedules.js',
            array('jquery'),
            $this->version,
            true
        );
        wp_add_inline_script(
            $this->plugin_name . '-schedule',
            'const cmsSchedule = ' . json_encode(array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cms_schedule_nonce')
            )),
            'before'
        );
       
    
        wp_add_inline_script(
            $this->plugin_name . '-location',
            'const cmsAjax = ' . json_encode(array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cms_nonce')
            )),
            'before'
        );
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            __('Caregiver Management', 'caregiver-management-system'),
            __('Caregivers', 'caregiver-management-system'),
            'manage_options',
            'cms-dashboard',
            array($this, 'display_plugin_dashboard'),
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'cms-dashboard',
            __('Add New Caregiver', 'caregiver-management-system'),
            __('Add New', 'caregiver-management-system'),
            'manage_options',
            'cms-add-caregiver',
            array($this, 'display_add_caregiver')
        );

        add_submenu_page(
            'cms-dashboard',
            __('Schedules', 'caregiver-management-system'),
            __('Schedules', 'caregiver-management-system'),
            'manage_options',
            'cms-schedules',
            array($this, 'display_schedules')
        );

        add_submenu_page(
            'cms-dashboard',
            __('Payroll', 'caregiver-management-system'),
            __('Payroll', 'caregiver-management-system'),
            'manage_options',
            'cms-payroll',
            array($this, 'display_payroll')
        );
    }

    public function display_plugin_dashboard() {
        $file_path = plugin_dir_path(__FILE__) . 'partials/dashboard.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            echo 'Partial file not found: ' . $file_path;
        }
    }

    public function display_add_caregiver() {
        $file_path = plugin_dir_path(__FILE__) . 'partials/add-caregiver.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            echo 'Partial file not found: ' . $file_path;
        }
    }

    public function display_schedules() {
        $file_path = plugin_dir_path(__FILE__) . 'partials/schedules.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            echo 'Partial file not found: ' . $file_path;
        }
    }

    public function display_payroll() {
        $file_path = plugin_dir_path(__FILE__) . 'partials/payroll.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            echo 'Partial file not found: ' . $file_path;
        }
    }
}