<?php
if (!defined('ABSPATH')) {
    exit;
}
// Manually include the Statistics class
require_once __DIR__ . '../../../includes/models/class-qualifications.php';
require_once __DIR__ . '../../../includes/models/class-locations.php';
require_once __DIR__ . '../../../includes/models/class-skills.php';
require_once __DIR__ . '../../../includes/models/class-languages.php';



use CMS\Models\Qualifications;
use CMS\Models\Locations;
use CMS\Models\Skills;
use CMS\Models\Languages;
?>

<div class="wrap cms-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="cms-card">
        <form class="cms-form" id="add-caregiver-form">
            <?php wp_nonce_field('cms_add_caregiver', 'cms_nonce'); ?>
            <input type="hidden" name="action" value="cms_add_caregiver">

            <div class="cms-form-section">
                <h2><?php _e('Personal Information', 'caregiver-management-system'); ?></h2>
                
                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="first_name"><?php _e('First Name', 'caregiver-management-system'); ?></label>
                        <input type="text" id="first_name" name="first_name" class="cms-form-control" required>
                    </div>

                    <div class="cms-form-group">
                        <label for="last_name"><?php _e('Last Name', 'caregiver-management-system'); ?></label>
                        <input type="text" id="last_name" name="last_name" class="cms-form-control" required>
                    </div>
                </div>

                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="email"><?php _e('Email', 'caregiver-management-system'); ?></label>
                        <input type="email" id="email" name="email" class="cms-form-control" required>
                    </div>

                    <div class="cms-form-group">
                        <label for="date_of_birth"><?php _e('Date of Birth', 'caregiver-management-system'); ?></label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="cms-form-control" required>
                    </div>

                    <div class="cms-form-group">
                        <label for="gender"><?php _e('Gender', 'caregiver-management-system'); ?></label>
                        <select id="gender" name="gender" class="cms-form-control" required>
                            <option value=""><?php _e('Select Gender', 'caregiver-management-system'); ?></option>
                            <option value="male"><?php _e('Male', 'caregiver-management-system'); ?></option>
                            <option value="female"><?php _e('Female', 'caregiver-management-system'); ?></option>
                            <option value="other"><?php _e('Other', 'caregiver-management-system'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="cms-form-section">
                <h2><?php _e('Contact Information', 'caregiver-management-system'); ?></h2>
                
                <div class="cms-form-group">
                    <label for="contact_address"><?php _e('Contact Address', 'caregiver-management-system'); ?></label>
                    <textarea id="contact_address" name="contact_address" class="cms-form-control" required></textarea>
                </div>

                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="province"><?php _e('Province', 'caregiver-management-system'); ?></label>
                        <select id="province" name="province" class="cms-form-control" required>
                            <option value=""><?php _e('Select Province', 'caregiver-management-system'); ?></option>
                            <?php foreach (Locations::get_all_provinces() as $province) : ?>
                                <option value="<?php echo esc_attr($province->id); ?>">
                                    <?php echo esc_html($province->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="cms-form-group">
                        <label for="city"><?php _e('City', 'caregiver-management-system'); ?></label>
                        <select id="city" name="city" class="cms-form-control" required>
                            <option value=""><?php _e('Select City', 'caregiver-management-system'); ?></option>
                        </select>
                    </div>

                    <div class="cms-form-group">
                        <label for="phone_number"><?php _e('Phone Number', 'caregiver-management-system'); ?></label>
                        <input type="tel" id="phone_number" name="phone_number" class="cms-form-control" 
                               pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" 
                               placeholder="123-456-7890" required>
                    </div>
                </div>
            </div>

            <div class="cms-form-section">
                <h2><?php _e('Identification & Work Authorization', 'caregiver-management-system'); ?></h2>
                
                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="sin_number"><?php _e('Social Insurance Number (SIN)', 'caregiver-management-system'); ?></label>
                        <input type="text" id="sin_number" name="sin_number" class="cms-form-control" 
                               pattern="[0-9]{9}" placeholder="123456789" required>
                    </div>

                    <div class="cms-form-group">
                        <label for="drivers_license"><?php _e('Driver\'s License Number', 'caregiver-management-system'); ?></label>
                        <input type="text" id="drivers_license" name="drivers_license" class="cms-form-control">
                    </div>

                    <div class="cms-form-group">
                        <label for="drivers_license_expiry"><?php _e('License Expiry Date', 'caregiver-management-system'); ?></label>
                        <input type="date" id="drivers_license_expiry" name="drivers_license_expiry" class="cms-form-control">
                    </div>
                </div>

                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="work_status"><?php _e('Work Authorization Status', 'caregiver-management-system'); ?></label>
                        <select id="work_status" name="work_status" class="cms-form-control" required>
                            <option value=""><?php _e('Select Status', 'caregiver-management-system'); ?></option>
                            <option value="citizen"><?php _e('Citizen', 'caregiver-management-system'); ?></option>
                            <option value="permanent_resident"><?php _e('Permanent Resident', 'caregiver-management-system'); ?></option>
                            <option value="work_permit"><?php _e('Work Permit', 'caregiver-management-system'); ?></option>
                            <option value="other"><?php _e('Other', 'caregiver-management-system'); ?></option>
                        </select>
                    </div>

                    <div class="cms-form-group work-permit-expiry" style="display: none;">
                        <label for="work_permit_expiry"><?php _e('Work Permit Expiry Date', 'caregiver-management-system'); ?></label>
                        <input type="date" id="work_permit_expiry" name="work_permit_expiry" class="cms-form-control">
                    </div>
                </div>
            </div>

            <div class="cms-form-section">
                <h2><?php _e('Qualifications & Experience', 'caregiver-management-system'); ?></h2>
                
                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="qualification"><?php _e('Qualification', 'caregiver-management-system'); ?></label>
                        <select id="qualification" name="qualification" class="cms-form-control" required>
                            <option value=""><?php _e('Select Qualification', 'caregiver-management-system'); ?></option>
                            <?php foreach (Qualifications::get_all_qualifications() as $qualification) : ?>
                                <option value="<?php echo esc_attr($qualification->id); ?>">
                                    <?php echo esc_html($qualification->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="cms-form-group">
                        <label for="background_check_date"><?php _e('Background Check Date', 'caregiver-management-system'); ?></label>
                        <input type="date" id="background_check_date" name="background_check_date" class="cms-form-control" required>
                    </div>

                    <div class="cms-form-group">
                        <label for="background_check_status"><?php _e('Background Check Status', 'caregiver-management-system'); ?></label>
                        <select id="background_check_status" name="background_check_status" class="cms-form-control" required>
                            <option value=""><?php _e('Select Status', 'caregiver-management-system'); ?></option>
                            <option value="passed"><?php _e('Passed', 'caregiver-management-system'); ?></option>
                            <option value="pending"><?php _e('Pending', 'caregiver-management-system'); ?></option>
                            <option value="failed"><?php _e('Failed', 'caregiver-management-system'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="cms-form-group">
                    <label for="work_experience"><?php _e('Work Experience', 'caregiver-management-system'); ?></label>
                    <textarea id="work_experience" name="work_experience" class="cms-form-control" rows="4" required></textarea>
                </div>

                <!-- Skills Section -->
            <div class="cms-form-section">
                <h2><?php _e('Skills & Specializations', 'caregiver-management-system'); ?></h2>
                <?php
                $categories = Skills::get_categories();
                foreach ($categories as $category) :
                    $skills = Skills::get_by_category($category);
                ?>
                    <div class="cms-skills-category">
                        <h3><?php echo esc_html(ucfirst($category)); ?></h3>
                        <div class="cms-skills-grid">
                            <?php foreach ($skills as $skill) : ?>
                                <div class="cms-skill-item">
                                    <label>
                                        <input type="checkbox" name="skills[]" value="<?php echo esc_attr($skill->id); ?>">
                                        <?php echo esc_html($skill->name); ?>
                                    </label>
                                    <select name="skill_level_<?php echo esc_attr($skill->id); ?>" class="cms-skill-level">
                                        <option value="basic"><?php _e('Basic', 'caregiver-management-system'); ?></option>
                                        <option value="intermediate"><?php _e('Intermediate', 'caregiver-management-system'); ?></option>
                                        <option value="advanced"><?php _e('Advanced', 'caregiver-management-system'); ?></option>
                                        <option value="expert"><?php _e('Expert', 'caregiver-management-system'); ?></option>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

               <!-- Languages Section -->
            <div class="cms-form-section">
                <h2><?php _e('Languages', 'caregiver-management-system'); ?></h2>
                <div class="cms-languages-grid">
                    <?php
                    $languages = Languages::get_all();
                    foreach ($languages as $language) :
                    ?>
                        <div class="cms-language-item">
                            <label>
                                <input type="checkbox" name="languages[]" value="<?php echo esc_attr($language->id); ?>">
                                <?php echo esc_html($language->name); ?>
                            </label>
                            <select name="language_level_<?php echo esc_attr($language->id); ?>" class="cms-language-level">
                                <option value="basic"><?php _e('Basic', 'caregiver-management-system'); ?></option>
                                <option value="intermediate"><?php _e('Intermediate', 'caregiver-management-system'); ?></option>
                                <option value="fluent"><?php _e('Fluent', 'caregiver-management-system'); ?></option>
                                <option value="native"><?php _e('Native', 'caregiver-management-system'); ?></option>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cms-form-section">
                <h2><?php _e('Emergency Contact', 'caregiver-management-system'); ?></h2>
                
                <div class="cms-form-row">
                    <div class="cms-form-group">
                        <label for="emergency_contact_name"><?php _e('Contact Name', 'caregiver-management-system'); ?></label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="cms-form-control" required>
                    </div>

                    <div class="cms-form-group">
                        <label for="emergency_contact_relationship"><?php _e('Relationship', 'caregiver-management-system'); ?></label>
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" class="cms-form-control" required>
                    </div>
                </div>

                <div class="cms-form-group">
                    <label for="emergency_contact_address"><?php _e('Emergency Contact Address', 'caregiver-management-system'); ?></label>
                    <textarea id="emergency_contact_address" name="emergency_contact_address" class="cms-form-control" required></textarea>
                </div>

                <div class="cms-form-group">
                    <label for="emergency_phone_number"><?php _e('Emergency Phone Number', 'caregiver-management-system'); ?></label>
                    <input type="tel" id="emergency_phone_number" name="emergency_phone_number" class="cms-form-control" 
                           pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" 
                           placeholder="123-456-7890" required>
                </div>
            </div>

            <div class="cms-form-section">
                <h2><?php _e('Employment Details', 'caregiver-management-system'); ?></h2>
                
                <div class="cms-form-group">
                    <label for="hourly_rate"><?php _e('Hourly Rate', 'caregiver-management-system'); ?></label>
                    <input type="number" id="hourly_rate" name="hourly_rate" class="cms-form-control" 
                           step="0.01" min="0" required>
                </div>
            </div>

            <button type="submit" class="button button-primary">
                <?php _e('Add Caregiver', 'caregiver-management-system'); ?>
            </button>
        </form>
    </div>
</div>