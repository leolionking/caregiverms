<?php
namespace CMS\Admin;

require_once __DIR__ . '../../includes/models/class-caregiver.php';
use CMS\Models\Caregiver;

class Ajax_Schedules {
    public static function init() {
        // Debug log for initialization
        error_log('Ajax_Schedules initialization started');
        
        // Register AJAX actions
        add_action('wp_ajax_cms_get_assigned_caregivers', [self::class, 'cms_get_assigned_caregivers']);
        add_action('wp_ajax_nopriv_cms_get_assigned_caregivers', [self::class, 'cms_get_assigned_caregivers']);
        add_action('wp_ajax_cms_get_caregivers', [self::class, 'cms_get_caregivers']);
        add_action('wp_ajax_nopriv_cms_get_caregivers', [self::class, 'cms_get_caregivers']);
        add_action('wp_ajax_cms_assign_caregiver', [self::class, 'cms_assign_caregiver']);
        add_action('wp_ajax_nopriv_cms_assign_caregiver', [self::class, 'cms_assign_caregiver']);

        // Add script localization
        add_action('wp_enqueue_scripts', [self::class, 'localize_script']);
        
        error_log('Ajax_Schedules initialization completed');
    }

    public static function localize_script() {
        wp_localize_script('cms-schedule-script', 'cmsSchedule', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cms_schedule_nonce')
        ));
    }

    private static function convertDayToDate($day) {
        $dayMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6
        ];

        $day = strtolower($day);

        if (!isset($dayMap[$day])) {
            throw new \Exception("Invalid day_2: $day");
        }

        $today = new \DateTime();
        $currentWeekday = (int)$today->format('w');
        $targetWeekday = $dayMap[$day];
        $dayDifference = $targetWeekday - $currentWeekday;
        
        $targetDate = clone $today;
        $targetDate->modify("$dayDifference days");

        return $targetDate->format('Y/m/d');
    }

    public static function cms_get_assigned_caregivers() {
        // Debug logging
        error_log('POST data: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!check_ajax_referer('cms_schedule_nonce', 'nonce', false)) {
            error_log('Nonce verification failed for cms_get_assigned_caregivers');
            wp_send_json_error(['message' => __('Security check failed.', 'caregiver-management-system')]);
        }

        $day = sanitize_text_field($_POST['day']);
        $shift = sanitize_text_field($_POST['shift']);

        try {
            error_log("Processing request for day: $day, shift: $shift");
            
            // Get assigned caregivers
            $caregivers = Caregiver::get_assigned_caregivers($day, $shift);
            
            if ($caregivers === false || empty($caregivers)) {
                wp_send_json_success(['caregivers' => []]);
            } else {
                wp_send_json_success(['caregivers' => $caregivers]);
            }
        } catch (\Exception $e) {
            error_log("CMS Schedule Error (get_assigned_caregivers): " . $e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
        
        wp_die();
    }

    public static function cms_get_caregivers() {
        if (!check_ajax_referer('cms_schedule_nonce', 'nonce', false)) {
            error_log('Nonce verification failed for cms_get_caregivers');
            wp_send_json_error(['message' => __('Security check failed.', 'caregiver-management-system')]);
        }

        try {
            error_log("Processing request for cms_get_caregivers");
            $caregivers = Caregiver::get_all_caregivers();

            if ($caregivers === false || empty($caregivers)) {
                wp_send_json_success(['caregivers' => []]);
            }else {
                $formatted_caregivers = array_map(function($caregiver) {
                    return [
                        'id' => $caregiver->id,
                        'name' => $caregiver->caregiver_number
                    ];
                }, $caregivers);

                wp_send_json_success(['caregivers' => $formatted_caregivers ?: []]);
            }
            

            
        } catch (\Exception $e) {
            error_log("CMS Schedule Error (get_caregivers): " . $e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
        
        wp_die();
    }

    public static function cms_assign_caregiver() {
        // Debug logging
        error_log('POST data: ' . print_r($_POST, true));
    
        // Verify nonce
        if (!check_ajax_referer('cms_schedule_nonce', 'nonce', false)) {
            error_log('Nonce verification failed for cms_assign_caregiver');
            wp_send_json_error(['message' => __('Security check failed.', 'caregiver-management-system')]);
        }
    
        // Sanitize input data
        $day = sanitize_text_field($_POST['day']);
        $shift = sanitize_text_field($_POST['shift']);
        $caregiver_id = intval($_POST['caregiver_id']);
    
        try {
            error_log("Assigning caregiver: $caregiver_id to day: $day, shift: $shift");
    
            // Convert day to date
            $date = self::convertDayToDate($day);
    
            // Insert assigned caregiver
            $insert_success = Caregiver::insert_assigned_caregiver($date, $shift, $caregiver_id);
    
            // Check if the insertion was successful
            if ($insert_success) {
                wp_send_json_success([
                    'message' => __('Caregiver assigned successfully.', 'caregiver-management-system')
                ]);
            } else {
                error_log("Failed to assign caregiver: $caregiver_id to day: $day, shift: $shift");
                wp_send_json_error([
                    'message' => __('Failed to assign caregiver. Please try again.', 'caregiver-management-system')
                ]);
            }
        } catch (\Exception $e) {
            // Log and handle exceptions
            error_log("CMS Schedule Error (assign_caregiver): " . $e->getMessage());
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    
        // Ensure the function terminates properly
        wp_die();
    }
}

// Initialize on WordPress init
add_action('init', [Ajax_Schedules::class, 'init']);