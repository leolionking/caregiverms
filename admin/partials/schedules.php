<?php
if (!defined('ABSPATH')) {
    exit;
}


$current_week = isset($_GET['week']) ? intval($_GET['week']) : date('W');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
?>

<div class="wrap cms-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="cms-card">
        <div class="cms-schedule-controls">
            <form method="get" class="cms-schedule-filter">
                <input type="hidden" name="page" value="cms-schedules">
                <select name="week" id="week">
                    <?php
                    for ($week = 1; $week <= 52; $week++) {
                        printf(
                            '<option value="%d" %s>%s</option>',
                            $week,
                            selected($week, $current_week, false),
                            sprintf(__('Week %d', 'caregiver-management-system'), $week)
                        );
                    }
                    ?>
                </select>
                <select name="year" id="year">
                    <?php
                    $year_range = range(date('Y') - 1, date('Y') + 1);
                    foreach ($year_range as $year) {
                        printf(
                            '<option value="%d" %s>%d</option>',
                            $year,
                            selected($year, $current_year, false),
                            $year
                        );
                    }
                    ?>
                </select>
                <button type="submit" class="button"><?php _e('View Schedule', 'caregiver-management-system'); ?></button>
            </form>
            <div class="cms-schedule-actions">
                <button type="button" class="button button-primary" id="generate-schedule">
                    <?php _e('Generate Schedule', 'caregiver-management-system'); ?>
                </button>
                <button type="button" class="button" id="export-schedule">
                    <?php _e('Export Schedule', 'caregiver-management-system'); ?>
                </button>
            </div>
        </div>

        <div class="cms-schedule-grid">
            <?php
            $days = array(
                'monday' => __('Monday', 'caregiver-management-system'),
                'tuesday' => __('Tuesday', 'caregiver-management-system'),
                'wednesday' => __('Wednesday', 'caregiver-management-system'),
                'thursday' => __('Thursday', 'caregiver-management-system'),
                'friday' => __('Friday', 'caregiver-management-system'),
                'saturday' => __('Saturday', 'caregiver-management-system'),
                'sunday' => __('Sunday', 'caregiver-management-system')
            );

            $shifts = array(
                'morning' => __('Morning', 'caregiver-management-system'),
                'afternoon' => __('Afternoon', 'caregiver-management-system'),
                'night' => __('Night', 'caregiver-management-system')
            );
            ?>

            <table class="cms-schedule-table">
                <thead>
                    <tr>
                        <th></th>
                        <?php foreach ($days as $day_key => $day_label) : ?>
                            <th><?php echo esc_html($day_label); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shifts as $shift_key => $shift_label) : ?>
                        <tr>
                            <td class="cms-shift-label"><?php echo esc_html($shift_label); ?></td>
                            <?php foreach ($days as $day_key => $day_label) : ?>
                                <td class="cms-schedule-cell" data-day="<?php echo esc_attr($day_key); ?>" data-shift="<?php echo esc_attr($shift_key); ?>">
                                    <div class="cms-assigned-caregivers">
                                        <!-- Dynamically populated by JavaScript -->
                                    </div>
                                    <button type="button" class="button button-small cms-assign-caregiver">
                                        <span class="dashicons dashicons-plus"></span>
                                    </button>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assignment Modal -->
    <div id="cms-assignment-modal" class="cms-modal">
        <div class="cms-modal-content">
            <h2><?php _e('Assign Caregiver', 'caregiver-management-system'); ?></h2>
            <div class="cms-modal-body">
                <select id="caregiver-select" class="cms-form-control">
                    <!-- Dynamically populated by JavaScript -->
                    
                </select>
            </div>
            <div class="cms-modal-footer">
                <button type="button" class="button" id="cancel-assignment">
                    <?php _e('Cancel', 'caregiver-management-system'); ?>
                </button>
                <button type="button" class="button button-primary" id="confirm-assignment">
                    <?php _e('Assign', 'caregiver-management-system'); ?>
                </button>
            </div>
        </div>
    </div>
</div>