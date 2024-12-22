<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Get global wpdb class
global $wpdb;

// Remove plugin tables
$tables = array(
    $wpdb->prefix . 'cms_schedules',
    $wpdb->prefix . 'cms_timesheets',
    $wpdb->prefix . 'cms_availability',
    $wpdb->prefix . 'cms_caregivers'
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// Remove plugin options
delete_option('cms_version');
delete_option('cms_settings');

// Remove caregiver role
remove_role('caregiver');