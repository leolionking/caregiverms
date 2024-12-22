<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap cms-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="cms-card">
        <h2><?php _e('Import Caregivers from CSV', 'caregiver-management-system'); ?></h2>
        
        <div class="cms-import-instructions">
            <h3><?php _e('CSV Format Requirements:', 'caregiver-management-system'); ?></h3>
            <ul>
                <li><?php _e('First Name (required)', 'caregiver-management-system'); ?></li>
                <li><?php _e('Last Name (required)', 'caregiver-management-system'); ?></li>
                <li><?php _e('Email (required, must be unique)', 'caregiver-management-system'); ?></li>
                <li><?php _e('Qualification (must match existing qualification)', 'caregiver-management-system'); ?></li>
                <li><?php _e('Province (must exist in system)', 'caregiver-management-system'); ?></li>
                <li><?php _e('State (must exist for province)', 'caregiver-management-system'); ?></li>
                <li><?php _e('City (must exist for state)', 'caregiver-management-system'); ?></li>
                <li><?php _e('Hourly Rate (numeric, greater than 0)', 'caregiver-management-system'); ?></li>
            </ul>
            
            <p>
                <a href="#" class="button" id="download-template">
                    <?php _e('Download Template', 'caregiver-management-system'); ?>
                </a>
            </p>
        </div>

        <form class="cms-import-form" id="import-caregivers-form" enctype="multipart/form-data">
            <?php wp_nonce_field('cms_import_caregivers', 'cms_nonce'); ?>
            <input type="hidden" name="action" value="cms_import_caregivers">

            <div class="cms-form-group">
                <label for="csv_file"><?php _e('Choose CSV File', 'caregiver-management-system'); ?></label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
            </div>

            <div class="cms-import-preview">
                <h3><?php _e('Preview', 'caregiver-management-system'); ?></h3>
                <div id="preview-container"></div>
            </div>

            <div class="cms-import-validation">
                <h3><?php _e('Validation Results', 'caregiver-management-system'); ?></h3>
                <div id="validation-results"></div>
            </div>

            <button type="submit" class="button button-primary" id="import-button" disabled>
                <?php _e('Import Caregivers', 'caregiver-management-system'); ?>
            </button>
        </form>
    </div>
</div>