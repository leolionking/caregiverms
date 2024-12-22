<?php
namespace CMS\Admin;

class Statistics {
    public static function get_total_caregivers() {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cms_caregivers");
    }

    public static function get_active_shifts() {
        global $wpdb;
        $today = date('Y-m-d');
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cms_schedules 
            WHERE shift_date = %s AND status = 'scheduled'",
            $today
        ));
    }

    public static function get_weekly_hours() {
        global $wpdb;
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(total_hours), 0) 
            FROM {$wpdb->prefix}cms_timesheets 
            WHERE DATE(clock_in) BETWEEN %s AND %s",
            $week_start,
            $week_end
        ));
    }
}