<?php
namespace CMS\Database;

class Tables {
    public static function create_all() {
        self::create_provinces_table();
        self::create_cities_table();
        self::create_qualifications_table();
        self::create_caregivers_table();
        self::create_schedules_table();
        self::create_timesheets_table();
        self::create_availability_table();
        self::create_activity_log_table();
        self::create_emergency_contacts_table();
        self::create_skills_table();
        self::create_caregiver_skills_table();
        self::create_languages_table();
        self::create_caregiver_languages_table();
        self::add_foreign_key_constraints();
        self::insert_languages();
        self::insert_skills();
    }

    private static function create_provinces_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_provinces` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_unique` (`name`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_cities_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_cities` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `province_id` BIGINT(20) UNSIGNED NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `location_unique` (`province_id`, `name`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_caregivers_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_caregivers` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `caregiver_number` VARCHAR(20) NOT NULL,
            `user_id` BIGINT(20) UNSIGNED NOT NULL,
            `date_of_birth` DATE NOT NULL,
            `gender` ENUM('male', 'female', 'other') NOT NULL,
            `contact_address` TEXT NOT NULL,
            `province_id` BIGINT(20) UNSIGNED NOT NULL,
            `city_id` BIGINT(20) UNSIGNED NOT NULL,
            `phone_number` VARCHAR(20) NOT NULL,
            `sin_number` VARCHAR(11) NOT NULL,
            `drivers_license_number` VARCHAR(20),
            `drivers_license_expiry` DATE,
            `work_authorization_status` ENUM('citizen', 'permanent_resident', 'work_permit', 'other') NOT NULL,
            `work_permit_expiry` DATE,
            `work_experience` TEXT,
            `background_check_date` DATE,
            `background_check_status` ENUM('passed', 'pending', 'failed') NOT NULL,
            `qualification_id` BIGINT(20) UNSIGNED NOT NULL,
            `hourly_rate` DECIMAL(10,2) NOT NULL,
            `status` ENUM('active', 'inactive') DEFAULT 'active',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `caregiver_number_unique` (`caregiver_number`),
            UNIQUE KEY `user_unique` (`user_id`),
            UNIQUE KEY `sin_unique` (`sin_number`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_emergency_contacts_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_emergency_contacts` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `caregiver_id` BIGINT(20) UNSIGNED NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `relationship` VARCHAR(50) NOT NULL,
            `address` TEXT NOT NULL,
            `phone_number` VARCHAR(20) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_skills_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_skills` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `category` VARCHAR(50) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_unique` (`name`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_caregiver_skills_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_caregiver_skills` (
            `caregiver_id` BIGINT(20) UNSIGNED NOT NULL,
            `skill_id` BIGINT(20) UNSIGNED NOT NULL,
            `proficiency_level` ENUM('basic', 'intermediate', 'advanced', 'expert') NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`caregiver_id`, `skill_id`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_languages_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_languages` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL,
            `code` VARCHAR(5) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `code_unique` (`code`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_caregiver_languages_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_caregiver_languages` (
            `caregiver_id` BIGINT(20) UNSIGNED NOT NULL,
            `language_id` BIGINT(20) UNSIGNED NOT NULL,
            `proficiency_level` ENUM('basic', 'intermediate', 'fluent', 'native') NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`caregiver_id`, `language_id`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_qualifications_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_qualifications` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_unique` (`name`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_schedules_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_schedules` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `caregiver_id` BIGINT(20) UNSIGNED NOT NULL,
            `shift_date` DATE NOT NULL,
            `shift_type` ENUM('morning', 'afternoon', 'night') NOT NULL,
            `status` ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `shift_unique` (`caregiver_id`, `shift_date`, `shift_type`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_timesheets_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_timesheets` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `caregiver_id` BIGINT(20) UNSIGNED NOT NULL,
            `schedule_id` BIGINT(20) UNSIGNED,
            `clock_in` DATETIME NOT NULL,
            `clock_out` DATETIME,
            `total_hours` DECIMAL(5,2),
            `location_lat` DECIMAL(10,8),
            `location_lng` DECIMAL(11,8),
            `notes` TEXT,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_availability_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_availability` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `caregiver_id` BIGINT(20) UNSIGNED NOT NULL,
            `day_of_week` TINYINT NOT NULL,
            `shift_type` ENUM('morning', 'afternoon', 'night') NOT NULL,
            `available` BOOLEAN DEFAULT TRUE,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `availability_unique` (`caregiver_id`, `day_of_week`, `shift_type`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function create_activity_log_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cms_activity_log` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT(20) UNSIGNED NOT NULL,
            `action` VARCHAR(255) NOT NULL,
            `details` TEXT,
            `ip_address` VARCHAR(45),
            `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    

    private static function add_foreign_key_constraints() {
        global $wpdb;

        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_cities` ADD CONSTRAINT `fk_province_id` FOREIGN KEY (`province_id`) REFERENCES `{$wpdb->prefix}cms_provinces`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregivers` ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `{$wpdb->prefix}users`(`ID`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregivers` ADD CONSTRAINT `fk_qualification_id` FOREIGN KEY (`qualification_id`) REFERENCES `{$wpdb->prefix}cms_qualifications`(`id`);");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregivers` ADD CONSTRAINT `fk_province_id` FOREIGN KEY (`province_id`) REFERENCES `{$wpdb->prefix}cms_provinces`(`id`);");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregivers` ADD CONSTRAINT `fk_city_id` FOREIGN KEY (`city_id`) REFERENCES `{$wpdb->prefix}cms_cities`(`id`);");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_emergency_contacts` ADD CONSTRAINT `fk_caregiver_id` FOREIGN KEY (`caregiver_id`) REFERENCES `{$wpdb->prefix}cms_caregivers`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregiver_skills` ADD CONSTRAINT `fk_caregiver_id` FOREIGN KEY (`caregiver_id`) REFERENCES `{$wpdb->prefix}cms_caregivers`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregiver_skills` ADD CONSTRAINT `fk_skill_id` FOREIGN KEY (`skill_id`) REFERENCES `{$wpdb->prefix}cms_skills`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregiver_languages` ADD CONSTRAINT `fk_caregiver_id` FOREIGN KEY (`caregiver_id`) REFERENCES `{$wpdb->prefix}cms_caregivers`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_caregiver_languages` ADD CONSTRAINT `fk_language_id` FOREIGN KEY (`language_id`) REFERENCES `{$wpdb->prefix}cms_languages`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_schedules` ADD CONSTRAINT `fk_caregiver_id` FOREIGN KEY (`caregiver_id`) REFERENCES `{$wpdb->prefix}cms_caregivers`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_timesheets` ADD CONSTRAINT `fk_caregiver_id` FOREIGN KEY (`caregiver_id`) REFERENCES `{$wpdb->prefix}cms_caregivers`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_timesheets` ADD CONSTRAINT `fk_schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `{$wpdb->prefix}cms_schedules`(`id`) ON DELETE SET NULL;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_availability` ADD CONSTRAINT `fk_caregiver_id` FOREIGN KEY (`caregiver_id`) REFERENCES `{$wpdb->prefix}cms_caregivers`(`id`) ON DELETE CASCADE;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}cms_activity_log` ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `{$wpdb->prefix}users`(`ID`) ON DELETE CASCADE;");
    }
    private static function insert_default_data() {
        global $wpdb;

        // Sample provinces and cities
        $provinces = array(
            'Ontario' => array('Toronto', 'Ottawa', 'Hamilton', 'London', 'Windsor'),
            'British Columbia' => array('Vancouver', 'Victoria', 'Surrey', 'Burnaby', 'Richmond'),
            'Alberta' => array('Calgary', 'Edmonton', 'Red Deer', 'Lethbridge', 'Medicine Hat'),
            'Quebec' => array('Montreal', 'Quebec City', 'Laval', 'Gatineau', 'Longueuil')
        );

        foreach ($provinces as $province => $cities) {
            $wpdb->insert(
                $wpdb->prefix . 'cms_provinces',
                array('name' => $province),
                array('%s')
            );
            $province_id = $wpdb->insert_id;

            foreach ($cities as $city) {
                $wpdb->insert(
                    $wpdb->prefix . 'cms_cities',
                    array(
                        'province_id' => $province_id,
                        'name' => $city
                    ),
                    array('%d', '%s')
                );
            }
        }

    }

    public static function insert_skills() {
        global $wpdb;

        $skills = array(
            'medical' => array(
                'Medication Administration',
                'Vital Signs Monitoring',
                'Wound Care',
                'Diabetes Management',
                'Blood Pressure Monitoring',
                'Injection Administration',
                'First Aid',
                'CPR'
            ),
            'personal_care' => array(
                'Bathing Assistance',
                'Grooming',
                'Dressing Assistance',
                'Toileting Assistance',
                'Mobility Support',
                'Transfer Assistance',
                'Feeding Assistance',
                'Personal Hygiene'
            ),
            'specialized_care' => array(
                'Dementia Care',
                'Alzheimer\'s Care',
                'Palliative Care',
                'Post-Surgery Care',
                'Stroke Recovery',
                'Cancer Care',
                'Respiratory Care',
                'Physical Therapy Support'
            ),
            'household' => array(
                'Meal Preparation',
                'Light Housekeeping',
                'Laundry',
                'Grocery Shopping',
                'Medication Reminders',
                'Transportation',
                'Companionship',
                'Exercise Assistance'
            )
        );

        foreach ($skills as $category => $skill_list) {
            foreach ($skill_list as $skill) {
                $wpdb->insert(
                    $wpdb->prefix . 'cms_skills',
                    array(
                        'name' => $skill,
                        'category' => $category
                    ),
                    array('%s', '%s')
                );
            }
        }
    }

    public static function insert_languages() {
        global $wpdb;

        $languages = array(
            array('English', 'en'),
            array('French', 'fr'),
            array('Spanish', 'es'),
            array('Mandarin', 'zh'),
            array('Cantonese', 'yue'),
            array('Hindi', 'hi'),
            array('Punjabi', 'pa'),
            array('Urdu', 'ur'),
            array('Arabic', 'ar'),
            array('Portuguese', 'pt'),
            array('Italian', 'it'),
            array('German', 'de'),
            array('Russian', 'ru'),
            array('Korean', 'ko'),
            array('Japanese', 'ja'),
            array('Vietnamese', 'vi'),
            array('Tagalog', 'tl'),
            array('Greek', 'el'),
            array('Polish', 'pl'),
            array('Ukrainian', 'uk')
        );

        foreach ($languages as $language) {
            $wpdb->insert(
                $wpdb->prefix . 'cms_languages',
                array(
                    'name' => $language[0],
                    'code' => $language[1]
                ),
                array('%s', '%s')
            );
        }
    }
    
}