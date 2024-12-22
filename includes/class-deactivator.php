<?php
namespace CMS;

class Deactivator {
    public static function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hook('cms_daily_schedule_generation');
        wp_clear_scheduled_hook('cms_weekly_payroll_generation');
        
        // Don't remove tables or data on deactivation
        // Data cleanup should be handled by uninstall.php if needed
    }
}