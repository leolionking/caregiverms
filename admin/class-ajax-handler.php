<?php
namespace CMS\Admin;

require_once __DIR__ . '../../includes/models/class-locations.php';
use CMS\Models\Locations;

class Ajax_Handler {
    public static function init() {
        add_action('wp_ajax_cms_get_cities', array(__CLASS__, 'handle_get_cities'));
        add_action('wp_ajax_nopriv_cms_get_cities', array(__CLASS__, 'handle_get_cities'));
    }

    public static function handle_get_cities() {
        check_ajax_referer('cms_nonce', 'nonce');

        $province_id = isset($_GET['province_id']) ? intval($_GET['province_id']) : 0;
        
        if (empty($province_id)) {
            wp_send_json_error(array('message' => __('Invalid province ID', 'caregiver-management-system')));
        }

        $cities = Locations::get_cities($province_id);
        
        if (empty($cities)) {
            wp_send_json_error(array('message' => __('No cities found for this province', 'caregiver-management-system')));
        }

        wp_send_json_success($cities);
    }
}