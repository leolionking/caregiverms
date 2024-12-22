<?php
namespace CMS\Models;

class Authentication {
    public static function authenticate_caregiver($email, $password) {
        $user = get_user_by('email', $email);
        
        if (!$user || !wp_check_password($password, $user->user_pass, $user->ID)) {
            return new \WP_Error('invalid_credentials', __('Invalid email or password', 'caregiver-management-system'));
        }

        if (!in_array('caregiver', $user->roles)) {
            return new \WP_Error('not_caregiver', __('This account is not a caregiver account', 'caregiver-management-system'));
        }

        return $user;
    }

    public static function is_caregiver($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        $user = get_userdata($user_id);
        return $user && in_array('caregiver', $user->roles);
    }

    public static function get_caregiver_id($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cms_caregivers WHERE user_id = %d",
            $user_id
        ));
    }

    public static function require_caregiver() {
        if (!self::is_caregiver()) {
            wp_die(__('Access denied. This area is for caregivers only.', 'caregiver-management-system'));
        }
    }
}