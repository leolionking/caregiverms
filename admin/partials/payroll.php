<?php
if (!defined('ABSPATH')) {
    exit;
}

$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-t');
?>

<div class="wrap cms-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="cms-card">
        <div class="cms-payroll-controls">
            <form method="get" class="cms-payroll-filter">
                <input type="hidden" name="page" value="cms-payroll">
                <div class="cms-date-range">
                    <label for="start_date"><?php _e('Start Date:', 'caregiver-management-system'); ?></label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>" required>

                    <label for="end_date"><?php _e('End Date:', 'caregiver-management-system'); ?></label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>" required>
                </div>

                <div class="cms-filter-actions">
                    <button type="submit" class="button button-primary">
                        <?php _e('Generate Report', 'caregiver-management-system'); ?>
                    </button>
                    <button type="button" class="button" id="export-payroll">
                        <?php _e('Export Report', 'caregiver-management-system'); ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="cms-payroll-summary">
            <div class="cms-summary-card">
                <h3><?php _e('Total Hours', 'caregiver-management-system'); ?></h3>
                <div class="cms-summary-value" id="total-hours">0</div>
            </div>
            <div class="cms-summary-card">
                <h3><?php _e('Total Amount', 'caregiver-management-system'); ?></h3>
                <div class="cms-summary-value" id="total-amount">$0.00</div>
            </div>
            <div class="cms-summary-card">
                <h3><?php _e('Caregivers', 'caregiver-management-system'); ?></h3>
                <div class="cms-summary-value" id="total-caregivers">0</div>
            </div>
        </div>

        <div class="cms-payroll-table-container">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Caregiver', 'caregiver-management-system'); ?></th>
                        <th><?php _e('Qualification', 'caregiver-management-system'); ?></th>
                        <th><?php _e('Hours', 'caregiver-management-system'); ?></th>
                        <th><?php _e('Rate', 'caregiver-management-system'); ?></th>
                        <th><?php _e('Total', 'caregiver-management-system'); ?></th>
                        <th><?php _e('Actions', 'caregiver-management-system'); ?></th>
                    </tr>
                </thead>
                <tbody id="payroll-data">
                    <!-- Dynamically populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Timesheet Modal -->
    <div id="cms-timesheet-modal" class="cms-modal">
        <div class="cms-modal-content">
            <h2><?php _e('Timesheet Details', 'caregiver-management-system'); ?></h2>
            <div class="cms-modal-body">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'caregiver-management-system'); ?></th>
                            <th><?php _e('Clock In', 'caregiver-management-system'); ?></th>
                            <th><?php _e('Clock Out', 'caregiver-management-system'); ?></th>
                            <th><?php _e('Hours', 'caregiver-management-system'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="timesheet-details">
                        <!-- Dynamically populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="cms-modal-footer">
                <button type="button" class="button" id="close-timesheet">
                    <?php _e('Close', 'caregiver-management-system'); ?>
                </button>
            </div>
        </div>
    </div>
</div>