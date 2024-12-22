<?php
namespace CMS\Models;

class Caregiver {
    private $user_id;
    private $caregiver_id;

    public function __construct($user_id) {
        global $wpdb;
        
        $this->user_id = $user_id;
        $this->caregiver_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cms_caregivers WHERE user_id = %d",
            $this->user_id
        ));
    }
    public static function get_all_caregivers() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT id,caregiver_number 
            FROM {$wpdb->prefix}cms_caregivers 
            ORDER BY id"
        );
    }

    public static function get_assigned_caregivers($day, $shift) {
        global $wpdb;
        error_log("Attempting to fetch caregivers with parameters:");
        error_log("Day: " . print_r($day, true));
        error_log("Shift: " . print_r($shift, true));

        // Fetch caregivers assigned to the specific day and shift
        $query = $wpdb->get_results($wpdb->prepare(
            "SELECT c.id, c.caregiver_number AS name 
            FROM {$wpdb->prefix}cms_caregivers c
            JOIN {$wpdb->prefix}cms_schedules s ON c.id = s.caregiver_id
            WHERE s.shift_date = %s AND s.shift_type = %s
            ORDER BY c.id",
            $day,
            $shift
        ));

        
            error_log("Prepared SQL Query: " . $query);

            // Execute the query with error checking
            $results = $wpdb->get_results($query);

            // Log query results or potential errors
            if ($wpdb->last_error) {
                error_log("Database Error: " . $wpdb->last_error);
            }

            error_log("Query Results: " . print_r($results, true));

            return $results;
    }

    public function get_current_timesheet() {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cms_timesheets 
            WHERE caregiver_id = %d AND clock_out IS NULL
            ORDER BY clock_in DESC LIMIT 1",
            $this->caregiver_id
        ));
    }

    public function get_upcoming_shifts($limit = 5) {
        global $wpdb;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cms_schedules 
            WHERE caregiver_id = %d 
            AND shift_date >= CURDATE()
            AND status = 'scheduled'
            ORDER BY shift_date ASC, FIELD(shift_type, 'morning', 'afternoon', 'night')
            LIMIT %d",
            $this->caregiver_id,
            $limit
        ));
    }

    public function get_availability() {
        global $wpdb;
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT day_of_week, shift_type 
            FROM {$wpdb->prefix}cms_availability 
            WHERE caregiver_id = %d AND available = 1",
            $this->caregiver_id
        ));

        $availability = array();
        foreach ($results as $row) {
            $availability[$row->day_of_week][$row->shift_type] = true;
        }

        return $availability;
    }

    public function clock_in($lat, $lng) {
        global $wpdb;
        
        if ($this->get_current_timesheet()) {
            throw new \Exception(__('Already clocked in', 'caregiver-management-system'));
        }

        return $wpdb->insert(
            $wpdb->prefix . 'cms_timesheets',
            array(
                'caregiver_id' => $this->caregiver_id,
                'clock_in' => current_time('mysql'),
                'location_lat' => $lat,
                'location_lng' => $lng
            ),
            array('%d', '%s', '%f', '%f')
        );
    }

    public function clock_out($lat, $lng) {
        global $wpdb;
        
        $timesheet = $this->get_current_timesheet();
        if (!$timesheet) {
            throw new \Exception(__('Not clocked in', 'caregiver-management-system'));
        }

        $clock_in = new \DateTime($timesheet->clock_in);
        $clock_out = new \DateTime(current_time('mysql'));
        $interval = $clock_in->diff($clock_out);
        $hours = $interval->h + ($interval->i / 60);

        return $wpdb->update(
            $wpdb->prefix . 'cms_timesheets',
            array(
                'clock_out' => $clock_out->format('Y-m-d H:i:s'),
                'location_lat' => $lat,
                'location_lng' => $lng,
                'total_hours' => $hours
            ),
            array('id' => $timesheet->id),
            array('%s', '%f', '%f', '%f'),
            array('%d')
        );
    }
    public static function insert_assigned_caregiver($day, $shift, $caregiver_id) {
        global $wpdb;
    
        // Validate input
        if (empty($day) || empty($shift) || empty($caregiver_id)) {
            error_log("Invalid input for insert_assigned_caregiver: day=$day, shift=$shift, caregiver_id=$caregiver_id");
            return false;
        }
    
        // Perform the database insertion
        $result = $wpdb->insert(
            $wpdb->prefix . 'cms_schedules',
            array(
                'caregiver_id' => $caregiver_id,
                'shift_date' => $day,
                'shift_type' => $shift
            ),
            array('%d', '%s', '%s')
        );
    
        // Check if the insertion was successful
        if ($result === false) {
            // Log the error
            error_log("Failed to insert caregiver: caregiver_id=$caregiver_id, shift_date=$day, shift_type=$shift");
            error_log("Database error: " . $wpdb->last_error);
            return false;
        }
    
        // Return true if the insertion was successful
        return true;
    }

    public function toggle_availability($day, $shift) {
        global $wpdb;
        
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id, available FROM {$wpdb->prefix}cms_availability 
            WHERE caregiver_id = %d AND day_of_week = %d AND shift_type = %s",
            $this->caregiver_id,
            $day,
            $shift
        ));

        if ($existing) {
            return $wpdb->update(
                $wpdb->prefix . 'cms_availability',
                array('available' => !$existing->available),
                array('id' => $existing->id),
                array('%d'),
                array('%d')
            );
        } else {
            return $wpdb->insert(
                $wpdb->prefix . 'cms_availability',
                array(
                    'caregiver_id' => $this->caregiver_id,
                    'day_of_week' => $day,
                    'shift_type' => $shift,
                    'available' => 1
                ),
                array('%d', '%d', '%s', '%d')
            );
        }
    }
}