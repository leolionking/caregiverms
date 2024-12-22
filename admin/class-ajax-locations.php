<?php
namespace CMS\Models;

class Ajax_Locations {
    public static function init() {
        add_action('wp_ajax_cms_add_province', array(__CLASS__, 'handle_add_province'));
        add_action('wp_ajax_cms_add_city', array(__CLASS__, 'handle_add_city'));
        add_action('wp_ajax_cms_delete_province', array(__CLASS__, 'handle_delete_province'));
        add_action('wp_ajax_cms_delete_city', array(__CLASS__, 'handle_delete_city'));
        add_action('wp_ajax_cms_import_locations', array(__CLASS__, 'handle_import_locations'));
        add_action('wp_ajax_cms_get_cities', array(__CLASS__, 'handle_get_cities'));
    }

    public static function handle_add_province() {
        check_ajax_referer('cms_location_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'caregiver-management-system')));
        }

        $name = sanitize_text_field($_POST['name']);
        
        if (empty($name)) {
            wp_send_json_error(array('message' => __('Province name is required', 'caregiver-management-system')));
        }

        $result = Locations::add_province($name);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Province added successfully', 'caregiver-management-system')));
        } else {
            wp_send_json_error(array('message' => __('Failed to add province', 'caregiver-management-system')));
        }
    }

    public static function handle_add_city() {
        check_ajax_referer('cms_location_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'caregiver-management-system')));
        }

        $province_id = intval($_POST['province_id']);
        $name = sanitize_text_field($_POST['name']);
        
        if (empty($name) || empty($province_id)) {
            wp_send_json_error(array('message' => __('All fields are required', 'caregiver-management-system')));
        }

        $result = Locations::add_city($province_id, $name);
        
        if ($result) {
            wp_send_json_success(array('message' => __('City added successfully', 'caregiver-management-system')));
        } else {
            wp_send_json_error(array('message' => __('Failed to add city', 'caregiver-management-system')));
        }
    }

    public static function handle_delete_province() {
        check_ajax_referer('cms_location_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'caregiver-management-system')));
        }

        $id = intval($_POST['id']);
        
        if (empty($id)) {
            wp_send_json_error(array('message' => __('Invalid province ID', 'caregiver-management-system')));
        }

        $result = Locations::delete_province($id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Province deleted successfully', 'caregiver-management-system')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete province', 'caregiver-management-system')));
        }
    }

    public static function handle_delete_city() {
        check_ajax_referer('cms_location_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'caregiver-management-system')));
        }

        $id = intval($_POST['id']);
        
        if (empty($id)) {
            wp_send_json_error(array('message' => __('Invalid city ID', 'caregiver-management-system')));
        }

        $result = Locations::delete_city($id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('City deleted successfully', 'caregiver-management-system')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete city', 'caregiver-management-system')));
        }
    }

    public static function handle_get_cities() {
        check_ajax_referer('cms_location_nonce', 'nonce');
        
        $province_id = intval($_GET['province_id']);
        
        if (empty($province_id)) {
            wp_send_json_error(array('message' => __('Invalid province ID', 'caregiver-management-system')));
        }

        $cities = Locations::get_cities($province_id);
        wp_send_json_success($cities);
    }

    public static function handle_import_locations() {
        check_ajax_referer('cms_location_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'caregiver-management-system')));
        }

        if (!isset($_FILES['csv_file'])) {
            wp_send_json_error(array('message' => __('No file uploaded', 'caregiver-management-system')));
        }

        $file = $_FILES['csv_file'];
        $handle = fopen($file['tmp_name'], 'r');
        
        if (!$handle) {
            wp_send_json_error(array('message' => __('Could not read file', 'caregiver-management-system')));
        }

        $header = fgetcsv($handle);
        if ($header !== array('Province', 'City')) {
            fclose($handle);
            wp_send_json_error(array('message' => __('Invalid CSV format', 'caregiver-management-system')));
        }

        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            $imported = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                $province_name = sanitize_text_field($data[0]);
                $city_name = sanitize_text_field($data[1]);

                // Get or create province
                $province = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}cms_provinces WHERE name = %s",
                    $province_name
                ));

                if (!$province) {
                    Locations::add_province($province_name);
                    $province_id = $wpdb->insert_id;
                } else {
                    $province_id = $province->id;
                }

                // Add city
                Locations::add_city($province_id, $city_name);
                $imported++;
            }

            $wpdb->query('COMMIT');
            fclose($handle);
            
            wp_send_json_success(array(
                'message' => sprintf(__('%d locations imported successfully', 'caregiver-management-system'), $imported),
                'imported' => $imported
            ));
        } catch (\Exception $e) {
            $wpdb->query('ROLLBACK');
            fclose($handle);
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
}