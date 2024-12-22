<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '../../../includes/models/class-caregiver.php';


use CMS\Models\Caregiver;
$caregiver = new Caregiver(get_current_user_id());
$current_timesheet = $caregiver->get_current_timesheet();
$upcoming_shifts = $caregiver->get_upcoming_shifts();
$availability = $caregiver->get_availability();
?>

<div class="cms-portal-wrap">
    <div class="cms-portal-header">
        <h1><?php _e('Caregiver Portal', 'caregiver-management-system'); ?></h1>
        <div class="cms-user-info">
            <?php 
            $user = wp_get_current_user();
            printf(
                __('Welcome, %s', 'caregiver-management-system'),
                esc_html($user->display_name)
            ); 
            ?>
        </div>
    </div>

    <div class="cms-portal-grid">
        <!-- Clock In/Out Card -->
        <div class="cms-portal-card">
            <h3><?php _e('Time Clock', 'caregiver-management-system'); ?></h3>
            
            <?php if ($current_timesheet) : ?>
                <div class="cms-clock-status active">
                    <i class="dashicons dashicons-clock"></i>
                    <?php 
                    printf(
                        __('Clocked in at: %s', 'caregiver-management-system'),
                        date_i18n('g:i A', strtotime($current_timesheet->clock_in))
                    ); 
                    ?>
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
        </div>

        <!-- Today's Schedule Card -->
        <div class="cms-portal-card">
            <h3><?php _e('Today\'s Schedule', 'caregiver-management-system'); ?></h3>
            <?php 
            $today_shift = current(array_filter($upcoming_shifts, function($shift) {
                return date('Y-m-d') === $shift->shift_date;
            }));
            
            if ($today_shift) : ?>
                <div class="cms-today-shift">
                    <div class="cms-shift-time">
                        <i class="dashicons dashicons-calendar-alt"></i>
                        <span class="cms-shift-type <?php echo esc_attr($today_shift->shift_type); ?>">
                            <?php echo esc_html(ucfirst($today_shift->shift_type)); ?> Shift
                        </span>
                    </div>
                </div>
            <?php else : ?>
                <p class="cms-no-shift">
                    <?php _e('No shifts scheduled for today', 'caregiver-management-system'); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Upcoming Shifts Card -->
        <div class="cms-portal-card">
            <h3><?php _e('Upcoming Shifts', 'caregiver-management-system'); ?></h3>
            
            <?php if ($upcoming_shifts) : ?>
                <ul class="cms-upcoming-shifts">
                    <?php foreach ($upcoming_shifts as $shift) : ?>
                        <li class="cms-upcoming-shift">
                            <div class="cms-shift-date">
                                <i class="dashicons dashicons-calendar"></i>
                                <?php echo date_i18n('l, M j', strtotime($shift->shift_date)); ?>
                            </div>
                            <span class="cms-shift-type <?php echo esc_attr($shift->shift_type); ?>">
                                <?php echo esc_html(ucfirst($shift->shift_type)); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="cms-no-shifts">
                    <?php _e('No upcoming shifts scheduled', 'caregiver-management-system'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>