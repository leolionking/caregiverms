<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '../../../includes/models/class-caregiver.php';
use CMS\Models\Caregiver;

$caregiver = new Caregiver(get_current_user_id());
$current_timesheet = $caregiver->get_current_timesheet();
?>

<?php if ($current_timesheet) : ?>
    <div class="cms-clock-status active">
        <i class="dashicons dashicons-clock"></i>
        <?php 
        printf(
            __('Clocked in at: %s', 'caregiver-management-system'),
            date_i18n('g:i A', strtotime($current_timesheet->clock_in))
        ); 
        ?>
        <span class="cms-clock-duration">
            <?php
            $duration = human_time_diff(strtotime($current_timesheet->clock_in));
            printf(__('(%s ago)', 'caregiver-management-system'), $duration);
            ?>
        </span>
    </div>
    <button class="button cms-clock-button" data-action="clock_out">
        <i class="dashicons dashicons-exit"></i>
        <?php _e('Clock Out', 'caregiver-management-system'); ?>
    </button>
<?php else : ?>
    <div class="cms-clock-status inactive">
        <i class="dashicons dashicons-clock"></i>
        <?php _e('Not clocked in', 'caregiver-management-system'); ?>
    </div>
    <button class="button button-primary cms-clock-button" data-action="clock_in">
        <i class="dashicons dashicons-enter"></i>
        <?php _e('Clock In', 'caregiver-management-system'); ?>
    </button>
<?php endif; ?>