<?php
if (!defined('ABSPATH')) {
    exit;
}
// Manually include the Statistics class
require_once __DIR__ . '../../../includes/models/class-statistics.php';
require_once __DIR__ . '../../../includes/models/class-activity.php';

use CMS\Models\Statistics;
use CMS\Models\Activity;
?>

<div class="wrap cms-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="cms-grid">
        <div class="cms-stats-card">
            <h3><?php _e('Total Caregivers', 'caregiver-management-system'); ?></h3>
            <div class="cms-stats-number">
                <?php echo esc_html(Statistics::get_total_caregivers()); ?>
            </div>
        </div>

        <div class="cms-stats-card">
            <h3><?php _e('Active Shifts Today', 'caregiver-management-system'); ?></h3>
            <div class="cms-stats-number">
                <?php echo esc_html(Statistics::get_active_shifts()); ?>
            </div>
        </div>

        <div class="cms-stats-card">
            <h3><?php _e('This Week\'s Hours', 'caregiver-management-system'); ?></h3>
            <div class="cms-stats-number">
                <?php echo esc_html(Statistics::get_weekly_hours()); ?>
            </div>
        </div>
    </div>

    <div class="cms-card">
        <h2><?php _e('Recent Activity', 'caregiver-management-system'); ?></h2>
        <table class="cms-table">
            <thead>
                <tr>
                    <th><?php _e('Caregiver', 'caregiver-management-system'); ?></th>
                    <th><?php _e('Action', 'caregiver-management-system'); ?></th>
                    <th><?php _e('Time', 'caregiver-management-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_activity = Activity::get_recent();
                foreach ($recent_activity as $activity) :
                ?>
                <tr>
                    <td><?php echo esc_html($activity->caregiver_name); ?></td>
                    <td><?php echo esc_html($activity->action); ?></td>
                    <td><?php echo esc_html(human_time_diff(strtotime($activity->timestamp))); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>