<?php
namespace CMS\Models;

class CSV_Importer {
    private $errors = array();
    private $valid_records = array();
    
    public function validate_csv($file) {
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \Exception(__('Invalid file upload', 'caregiver-management-system'));
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            throw new \Exception(__('Could not read file', 'caregiver-management-system'));
        }

        // Read header row
        $headers = fgetcsv($handle);
        $required_headers = array(
            'first_name', 'last_name', 'email', 'qualification',
            'province', 'state', 'city', 'hourly_rate'
        );

        if (count(array_intersect($required_headers, array_map('strtolower', $headers))) !== count($required_headers)) {
            throw new \Exception(__('Invalid CSV format. Please use the provided template.', 'caregiver-management-system'));
        }

        $row = 2; // Start from row 2 (after headers)
        while (($data = fgetcsv($handle)) !== FALSE) {
            $record = array_combine($headers, $data);
            $validation_result = $this->validate_record($record, $row);
            
            if (empty($validation_result)) {
                $this->valid_records[] = $record;
            } else {
                $this->errors = array_merge($this->errors, $validation_result);
            }
            $row++;
        }

        fclose($handle);
        return array(
            'valid_count' => count($this->valid_records),
            'error_count' => count($this->errors),
            'errors' => $this->errors
        );
    }

    private function validate_record($record, $row) {
        $errors = array();

        // Basic validation
        if (empty($record['first_name'])) {
            $errors[] = sprintf(__('Row %d: First name is required', 'caregiver-management-system'), $row);
        }
        if (empty($record['last_name'])) {
            $errors[] = sprintf(__('Row %d: Last name is required', 'caregiver-management-system'), $row);
        }
        if (empty($record['email']) || !is_email($record['email'])) {
            $errors[] = sprintf(__('Row %d: Valid email is required', 'caregiver-management-system'), $row);
        }
        if (!is_numeric($record['hourly_rate']) || floatval($record['hourly_rate']) <= 0) {
            $errors[] = sprintf(__('Row %d: Valid hourly rate is required', 'caregiver-management-system'), $row);
        }

        // Check if email exists
        if (email_exists($record['email'])) {
            $errors[] = sprintf(__('Row %d: Email already exists', 'caregiver-management-system'), $row);
        }

        // Validate qualification
        $qualification = Qualifications::get_by_name($record['qualification']);
        if (!$qualification) {
            $errors[] = sprintf(__('Row %d: Invalid qualification', 'caregiver-management-system'), $row);
        }

        // Validate location
        if (!$this->validate_location($record['province'], $record['state'], $record['city'])) {
            $errors[] = sprintf(__('Row %d: Invalid location combination', 'caregiver-management-system'), $row);
        }

        return $errors;
    }

    private function validate_location($province, $state, $city) {
        global $wpdb;
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cms_locations 
            WHERE province = %s AND state = %s AND city = %s",
            $province, $state, $city
        )) > 0;
    }

    public function import() {
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            foreach ($this->valid_records as $record) {
                // Create WordPress user
                $user_data = array(
                    'user_login' => $record['email'],
                    'user_email' => $record['email'],
                    'user_pass' => wp_generate_password(),
                    'first_name' => $record['first_name'],
                    'last_name' => $record['last_name'],
                    'role' => 'caregiver'
                );

                $user_id = wp_insert_user($user_data);
                if (is_wp_error($user_id)) {
                    throw new \Exception($user_id->get_error_message());
                }

                // Get qualification ID
                $qualification = Qualifications::get_by_name($record['qualification']);

                // Create caregiver record
                $caregiver_data = array(
                    'user_id' => $user_id,
                    'qualification_id' => $qualification->id,
                    'province' => $record['province'],
                    'state' => $record['state'],
                    'city' => $record['city'],
                    'hourly_rate' => floatval($record['hourly_rate'])
                );

                $inserted = $wpdb->insert(
                    $wpdb->prefix . 'cms_caregivers',
                    $caregiver_data,
                    array('%d', '%d', '%s', '%s', '%s', '%f')
                );

                if ($inserted === false) {
                    throw new \Exception($wpdb->last_error);
                }

                // Send welcome email with password reset link
                wp_new_user_notification($user_id, null, 'both');
            }

            $wpdb->query('COMMIT');
            return count($this->valid_records);
        } catch (\Exception $e) {
            $wpdb->query('ROLLBACK');
            throw $e;
        }
    }
}