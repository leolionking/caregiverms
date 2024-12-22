<?php
if (!defined('ABSPATH')) {
    exit;
}


require_once __DIR__ . '../../../includes/models/class-locations.php';

use CMS\Models\Locations;
?>

<div class="wrap cms-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="cms-card">
        <div class="cms-locations-grid">
            <!-- Add Province Form -->
            <div class="cms-location-form">
                <h2><?php _e('Add Province', 'caregiver-management-system'); ?></h2>
                <form id="add-province-form">
                    <?php wp_nonce_field('cms_location_nonce', 'cms_nonce'); ?>
                    <div class="cms-form-group">
                        <label for="province_name"><?php _e('Province Name', 'caregiver-management-system'); ?></label>
                        <input type="text" id="province_name" name="name" class="regular-text" required>
                    </div>
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Province', 'caregiver-management-system'); ?>
                    </button>
                </form>
            </div>

            <!-- Add City Form -->
            <div class="cms-location-form">
                <h2><?php _e('Add City', 'caregiver-management-system'); ?></h2>
                <form id="add-city-form">
                    <?php wp_nonce_field('cms_location_nonce', 'cms_nonce'); ?>
                    <div class="cms-form-group">
                        <label for="city_province"><?php _e('Province', 'caregiver-management-system'); ?></label>
                        <select id="city_province" name="province_id" required>
                            <option value=""><?php _e('Select Province', 'caregiver-management-system'); ?></option>
                            <?php foreach (Locations::get_provinces() as $province) : ?>
                                <option value="<?php echo esc_attr($province->id); ?>">
                                    <?php echo esc_html($province->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cms-form-group">
                        <label for="city_name"><?php _e('City Name', 'caregiver-management-system'); ?></label>
                        <input type="text" id="city_name" name="name" class="regular-text" required>
                    </div>
                    <button type="submit" class="button button-primary">
                        <?php _e('Add City', 'caregiver-management-system'); ?>
                    </button>
                </form>
            </div>

            <!-- Import Locations -->
            <div class="cms-location-form">
                <h2><?php _e('Import Locations', 'caregiver-management-system'); ?></h2>
                <form id="import-locations-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('cms_location_nonce', 'cms_nonce'); ?>
                    <div class="cms-form-group">
                        <label for="csv_file"><?php _e('CSV File', 'caregiver-management-system'); ?></label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                        <p class="description">
                            <?php _e('CSV should have two columns: Province, City', 'caregiver-management-system'); ?>
                        </p>
                    </div>
                    <button type="submit" class="button button-primary">
                        <?php _e('Import', 'caregiver-management-system'); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Existing Locations -->
        <div class="cms-locations-list">
            <h2><?php _e('Existing Locations', 'caregiver-management-system'); ?></h2>
            <?php
            $provinces = Locations::get_provinces();
            if ($provinces) :
                foreach ($provinces as $province) :
                    $cities = Locations::get_cities($province->id);
            ?>
                <div class="cms-province-item">
                    <div class="cms-province-header">
                        <h3><?php echo esc_html($province->name); ?></h3>
                        <button type="button" class="button delete-province" data-id="<?php echo esc_attr($province->id); ?>">
                            <?php _e('Delete', 'caregiver-management-system'); ?>
                        </button>
                    </div>
                    <?php if ($cities) : ?>
                        <ul class="cms-cities-list">
                            <?php foreach ($cities as $city) : ?>
                                <li>
                                    <?php echo esc_html($city->name); ?>
                                    <button type="button" class="button delete-city" data-id="<?php echo esc_attr($city->id); ?>">
                                        <?php _e('Delete', 'caregiver-management-system'); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p><?php _e('No cities added yet.', 'caregiver-management-system'); ?></p>
                    <?php endif; ?>
                </div>
            <?php
                endforeach;
            else :
            ?>
                <p><?php _e('No locations added yet.', 'caregiver-management-system'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>