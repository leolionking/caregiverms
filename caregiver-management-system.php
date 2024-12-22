<?php
/**
 * Plugin Name: Caregiver Management System
 * Description: A comprehensive system for managing caregivers, schedules, and payroll
 * Version: 1.0.1
 * Author: Chinedu Madu
 * License: GPL v2 or later
 * Text Domain: caregiver-management-system
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('CMS_VERSION', '1.0.0');
define('CMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CMS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'CMS\\';
    $base_dir = CMS_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function cms_init() {
    require_once CMS_PLUGIN_DIR . 'includes/class-core.php';
    $plugin = new CMS\Core();
    $plugin->run();
}

// Hook into WordPress
add_action('plugins_loaded', 'cms_init');

// Activation hook to call the Activator class
register_activation_hook(__FILE__, 'cms_activate');
function cms_activate() {
    require_once CMS_PLUGIN_DIR . 'includes/class-activator.php';
    CMS\Activator::activate();
}