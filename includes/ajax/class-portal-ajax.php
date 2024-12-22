<?php
namespace CMS\Ajax;
require_once __DIR__ . '../../../includes/models/class-caregiver.php';


use CMS\Models\Caregiver;

class Portal_Ajax {
    public static function init() {
        add_action('wp_ajax_cms_clock_action', array(__CLASS__, 'handle_clock_action'));
        add_action('wp_ajax_cms_get_portal_data', array(__CLASS__, 'handle_get_portal_data'));
    }

    public static function handle_clock_action() {
        check_ajax_referer('cms_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Not logged in', 'caregiver-management-system')));
        }

        try {
            $caregiver = new Caregiver(get_current_user_id());
            $action = sanitize_text_field($_POST['clock_action']);
            $lat = floatval($_POST['lat']);
            $lng = floatval($_POST['lng']);

            if ($action === 'clock_in') {
                $caregiver->clock_in($lat, $lng);
                $message = __('Successfully clocked in', 'caregiver-management-system');
            } else {
                $caregiver->clock_out($lat, $lng);
                $message = __('Successfully clocked out', 'caregiver-management-system');
            }

            wp_send_json_success(array('message' => $message));
        } catch (\Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }

    public static function handle_get_portal_data() {
        check_ajax_referer('cms_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Not logged in', 'caregiver-management-system')));
        }

        try {
            $caregiver = new Caregiver(get_current_user_id());
            
            ob_start();
            include plugin_dir_path(__FILE__) . '../../public/partials/clock-status.php';
            $clock_status = ob_get_clean();

            ob_start();
            include plugin_dir_path(__FILE__) . '../../public/partials/upcoming-shifts.php';
            $upcoming_shifts = ob_get_clean();

            wp_send_json_success(array(
                'clock_status' => $clock_status,
                'upcoming_shifts' => $upcoming_shifts
            ));
        } catch (\Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
}