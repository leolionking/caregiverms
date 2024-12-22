<?php
namespace CMS\Ajax;

require_once __DIR__ . '../../../includes/models/class-caregiver.php';

use CMS\Models\Caregiver;

class Public_Ajax {
    public static function init() {
        // Register AJAX hooks for logged-in and non-logged-in users
        add_action('wp_ajax_cms_clock_action', array(__CLASS__, 'handle_clock_action'));
        add_action('wp_ajax_nopriv_cms_clock_action', array(__CLASS__, 'handle_clock_action'));

        add_action('wp_ajax_cms_toggle_availability', array(__CLASS__, 'handle_toggle_availability'));
        add_action('wp_ajax_nopriv_cms_toggle_availability', array(__CLASS__, 'handle_toggle_availability'));
    }

    /**
     * Handle the clock action (clock in/out)
     */
    public static function handle_clock_action() {
        // Verify the nonce to ensure the request is secure
        if (!check_ajax_referer('cms_public_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => __('Invalid nonce.', 'caregiver-management-system')));
        }

        // Ensure the user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Not logged in', 'caregiver-management-system')));
        }

        try {
            // Initialize the Caregiver model with the current user ID
            $caregiver = new Caregiver(get_current_user_id());

            // Get the action (clock_in or clock_out) and coordinates
            $action = sanitize_text_field($_POST['clock_action']);
            $lat = floatval($_POST['lat']);
            $lng = floatval($_POST['lng']);

            // Debugging logs
            error_log('Clock Action: ' . $action);
            error_log('Latitude: ' . $lat);
            error_log('Longitude: ' . $lng);

            // Validate coordinates
            if (!is_numeric($lat) || !is_numeric($lng)) {
                wp_send_json_error(array('message' => __('Invalid coordinates.', 'caregiver-management-system')));
            }

            // Perform the clock action
            if ($action === 'clock_in') {
                $caregiver->clock_in($lat, $lng);
                $message = __('Successfully clocked in', 'caregiver-management-system');
            } elseif ($action === 'clock_out') {
                $caregiver->clock_out($lat, $lng);
                $message = __('Successfully clocked out', 'caregiver-management-system');
            } else {
                wp_send_json_error(array('message' => __('Invalid clock action.', 'caregiver-management-system')));
            }

            // Send a success response
            wp_send_json_success(array('message' => $message));
        } catch (\Exception $e) {
            // Log and return any exceptions
            error_log('Error in handle_clock_action: ' . $e->getMessage());
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }

    /**
     * Handle the toggle availability action
     */
    public static function handle_toggle_availability() {
        // Verify the nonce to ensure the request is secure
        if (!check_ajax_referer('cms_public_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => __('Invalid nonce.', 'caregiver-management-system')));
        }

        // Ensure the user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Not logged in', 'caregiver-management-system')));
        }

        try {
            // Initialize the Caregiver model with the current user ID
            $caregiver = new Caregiver(get_current_user_id());

            // Get the day and shift from the request
            $day = intval($_POST['day']);
            $shift = sanitize_text_field($_POST['shift']);

            // Debugging logs
            error_log('Toggle Availability: Day ' . $day . ', Shift ' . $shift);

            // Validate input
            if (!is_int($day) || empty($shift)) {
                wp_send_json_error(array('message' => __('Invalid input.', 'caregiver-management-system')));
            }

            // Toggle availability
            $caregiver->toggle_availability($day, $shift);

            // Send a success response
            wp_send_json_success(array('message' => __('Availability updated successfully.', 'caregiver-management-system')));
        } catch (\Exception $e) {
            // Log and return any exceptions
            error_log('Error in handle_toggle_availability: ' . $e->getMessage());
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
}

// Initialize the Public_Ajax class
Public_Ajax::init();

