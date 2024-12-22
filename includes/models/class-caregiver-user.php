<?php
namespace CMS\Models;

class Caregiver_User {
    public static function create($data) {

        // Validate date_of_birth format
        if (!self::validate_date_format($data['date_of_birth'])) {
            throw new \Exception(__('Invalid date format for date_of_birth', 'caregiver-management-system'));
        }

        // Validate province and city IDs
        if (!self::validate_province_id($data['province'])) {
            throw new \Exception(__('Invalid province ID', 'caregiver-management-system'));
        }

        if (!self::validate_city_id($data['city'])) {
            throw new \Exception(__('Invalid city ID', 'caregiver-management-system'));
        }

        if (!self::validate_qualification_id($data['qualification'])) {
            throw new \Exception(__('Invalid qualification ID', 'caregiver-management-system'));
        }

        // Generate unique caregiver number
        $caregiver_number = self::generate_caregiver_number();
        
        // Create WP user
        $userdata = array(
            'user_login'    => $data['email'],
            'user_email'    => $data['email'],
            'user_pass'     => wp_generate_password(),
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'display_name'  => $data['first_name'] . ' ' . $data['last_name'],
            'role'          => 'caregiver'
        );

        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) {
            throw new \Exception($user_id->get_error_message());
        }

        // Insert caregiver details
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_caregivers';
        $insert_data = array(
                'caregiver_number' => $caregiver_number,
                'user_id' => $user_id,
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'contact_address' => $data['contact_address'],
                'province_id' => $data['province'],
                'city_id' => $data['city'],
                'phone_number' => $data['phone_number'],
                'sin_number' => $data['sin_number'],
                'drivers_license_number' => $data['drivers_license'],
                'drivers_license_expiry' => $data['drivers_license_expiry'],
                'work_authorization_status' => $data['work_status'],
                'work_permit_expiry' => $data['work_permit_expiry'],
                'work_experience' => $data['work_experience'],
                'background_check_date' => $data['background_check_date'],
                'background_check_status' => $data['background_check_status'],
                'qualification_id' => $data['qualification'],
                'hourly_rate' => $data['hourly_rate']
        );
            $format= array(
                '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', 
                '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%f'
            );

            // Debugging: Log the SQL query and data being inserted
            $insert_query = $wpdb->prepare(
                "INSERT INTO $table_name (caregiver_number, user_id, date_of_birth, gender, contact_address, province_id, city_id, phone_number, sin_number, drivers_license_number, drivers_license_expiry, work_authorization_status, work_permit_expiry, work_experience, background_check_date, background_check_status, qualification_id, hourly_rate) VALUES (%s, %d, %s, %s, %s, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %f)",
                $insert_data['caregiver_number'], $insert_data['user_id'], $insert_data['date_of_birth'], $insert_data['gender'], $insert_data['contact_address'], $insert_data['province_id'], $insert_data['city_id'], $insert_data['phone_number'], $insert_data['sin_number'], $insert_data['drivers_license_number'], $insert_data['drivers_license_expiry'], $insert_data['work_authorization_status'], $insert_data['work_permit_expiry'], $insert_data['work_experience'], $insert_data['background_check_date'], $insert_data['background_check_status'], $insert_data['qualification_id'], $insert_data['hourly_rate']
            );
            error_log('Insert Query: ' . $insert_query);
            error_log('Insert Data: ' . print_r($insert_data, true));
    
            $inserted = $wpdb->insert($table_name, $insert_data, $format);
    
            if ($inserted === false) {
                error_log('Failed to insert caregiver details: ' . $wpdb->last_error);
                wp_delete_user($user_id);
                throw new \Exception(__('Failed to create Employee record'. $wpdb->last_error, 'caregiver-management-system'));
            }
    
        $caregiver_id = $wpdb->insert_id;

        // Add emergency contact
        $wpdb->insert(
            $wpdb->prefix . 'cms_emergency_contacts',
            array(
                'caregiver_id' => $caregiver_id,
                'name' => $data['emergency_contact_name'],
                'relationship' => $data['emergency_contact_relationship'],
                'address' => $data['emergency_contact_address'],
                'phone_number' => $data['emergency_phone_number']
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        // Add skills
        if (!empty($data['skills'])) {
            foreach ($data['skills'] as $skill_id) {
                $level = $data['skill_level_' . $skill_id];
                $wpdb->insert(
                    $wpdb->prefix . 'cms_caregiver_skills',
                    array(
                        'caregiver_id' => $caregiver_id,
                        'skill_id' => $skill_id,
                        'proficiency_level' => $level
                    ),
                    array('%d', '%d', '%s')
                );
            }
        }

        // Add languages
        if (!empty($data['languages'])) {
            foreach ($data['languages'] as $language_id) {
                $level = $data['language_level_' . $language_id];
                $wpdb->insert(
                    $wpdb->prefix . 'cms_caregiver_languages',
                    array(
                        'caregiver_id' => $caregiver_id,
                        'language_id' => $language_id,
                        'proficiency_level' => $level
                    ),
                    array('%d', '%d', '%s')
                );
            }
        }

        // Send welcome email with login credentials
        wp_new_user_notification($user_id, null, 'both');

        return $caregiver_id;
    }
    private static function validate_date_format($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private static function validate_province_id($province_id) {
        global $wpdb;
        $province_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cms_provinces WHERE id = %d",
            $province_id
        ));
        return $province_exists > 0;
    }

    private static function validate_city_id($city_id) {
        global $wpdb;
        $city_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cms_cities WHERE id = %d",
            $city_id
        ));
        return $city_exists > 0;
    }

    private static function validate_qualification_id($qualification_id) {
        global $wpdb;
        $qualification_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cms_qualifications WHERE id = %d",
            $qualification_id
        ));
        return $qualification_exists > 0;
    }


    private static function generate_caregiver_number() {
        global $wpdb;
        
        do {
            $number = 'CG' . date('Y') . mt_rand(1000, 9999);
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}cms_caregivers WHERE caregiver_number = %s",
                $number
            ));
        } while ($exists > 0);

        return $number;
    }
}