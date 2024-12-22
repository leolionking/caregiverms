<?php
namespace CMS\Ajax;

require_once __DIR__ . '../../../includes/models/class-caregiver-user.php';


use CMS\Models\Caregiver_User;

class Caregiver_Ajax {
    public static function init() {
        add_action('wp_ajax_cms_add_caregiver', array(__CLASS__, 'handle_add_caregiver'));
    }

    public static function handle_add_caregiver() {
        check_ajax_referer('cms_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'caregiver-management-system')));
        }

        try {
            // Validate required fields
            $required_fields = array(
                'first_name' => __('First Name', 'caregiver-management-system'),
                'last_name' => __('Last Name', 'caregiver-management-system'),
                'email' => __('Email', 'caregiver-management-system'),
                'date_of_birth' => __('Date of Birth', 'caregiver-management-system'),
                'gender' => __('Gender', 'caregiver-management-system'),
                'contact_address' => __('Contact Address', 'caregiver-management-system'),
                'province' => __('Province', 'caregiver-management-system'),
                'city' => __('City', 'caregiver-management-system'),
                'qualification' => __('Qualification', 'caregiver-management-system'),
                'phone_number' => __('Phone Number', 'caregiver-management-system'),
                'sin_number' => __('SIN Number', 'caregiver-management-system')
            );

            foreach ($required_fields as $field => $label) {
                if (empty($_POST[$field])) {
                    throw new \Exception(sprintf(__('%s is required', 'caregiver-management-system'), $label));
                }
            }

            // Validate email
            if (!is_email($_POST['email'])) {
                throw new \Exception(__('Invalid email address', 'caregiver-management-system'));
            }

            if (email_exists($_POST['email'])) {
                throw new \Exception(__('Email already registered', 'caregiver-management-system'));
            }

            // Create caregiver
            $caregiver_id = Caregiver_User::create(array(
                'email' => sanitize_email($_POST['email']),
                'first_name' => sanitize_text_field($_POST['first_name']),
                'last_name' => sanitize_text_field($_POST['last_name']),
                'date_of_birth' => sanitize_text_field($_POST['date_of_birth']),
                'gender' => sanitize_text_field($_POST['gender']),
                'contact_address' => sanitize_textarea_field($_POST['contact_address']),
                'province' => intval($_POST['province']),
                'city' => intval($_POST['city']),
                'phone_number' => sanitize_text_field($_POST['phone_number']),
                'sin_number' => sanitize_text_field($_POST['sin_number']),
                'drivers_license' => sanitize_text_field($_POST['drivers_license']),
                'drivers_license_expiry' => sanitize_text_field($_POST['drivers_license_expiry']),
                'work_status' => sanitize_text_field($_POST['work_status']),
                'work_permit_expiry' => sanitize_text_field($_POST['work_permit_expiry']),
                'work_experience' => sanitize_textarea_field($_POST['work_experience']),
                'background_check_date' => sanitize_text_field($_POST['background_check_date']),
                'background_check_status' => sanitize_text_field($_POST['background_check_status']),
                'qualification' => intval($_POST['qualification']),
                'hourly_rate' => floatval($_POST['hourly_rate']),
                'skills' => isset($_POST['skills']) ? array_map('intval', $_POST['skills']) : array(),
                'skill_levels' => isset($_POST['skill_levels']) ? array_map('sanitize_text_field', $_POST['skill_levels']) : array(),
                'languages' => isset($_POST['languages']) ? array_map('intval', $_POST['languages']) : array(),
                'language_levels' => isset($_POST['language_levels']) ? array_map('sanitize_text_field', $_POST['language_levels']) : array(),
                'emergency_contact_name' => sanitize_text_field($_POST['emergency_contact_name']),
                'emergency_contact_relationship' => sanitize_text_field($_POST['emergency_contact_relationship']),
                'emergency_contact_address' => sanitize_textarea_field($_POST['emergency_contact_address']),
                'emergency_phone_number' => sanitize_text_field($_POST['emergency_phone_number'])
            ));

            wp_send_json_success(array(
                'message' => __('Caregiver added successfully', 'caregiver-management-system'),
                'redirect' => admin_url('admin.php?page=cms-dashboard')
            ));

        } catch (\Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
}