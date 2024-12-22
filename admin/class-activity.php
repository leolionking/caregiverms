<?php
namespace CMS\Admin;

class Activity {
    public static function get_recent($limit = 10) {
        global $wpdb;
        
        $query = $wpdb->prepare(
            "SELECT 
                a.id,
                CONCAT(u.display_name) as caregiver_name,
                a.action,
                a.timestamp
            FROM {$wpdb->prefix}cms_activity_log a
            JOIN {$wpdb->users} u ON a.user_id = u.ID
            ORDER BY a.timestamp DESC
            LIMIT %d",
            $limit
        );

        return $wpdb->get_results($query);
    }

    public static function log($user_id, $action) {
        global $wpdb;
        
        return $wpdb->insert(
            $wpdb->prefix . 'cms_activity_log',
            array(
                'user_id' => $user_id,
                'action' => $action,
                'timestamp' => current_time('mysql')
            ),
            array('%d', '%s', '%s')
        );
    }
}