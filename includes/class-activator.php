<?php
namespace CMS;
require_once __DIR__ . '/database/class-tables.php';
require_once __DIR__ . '/setup/class-roles.php';
require_once __DIR__ . '/setup/class-email-templates.php';


use CMS\Database\Tables;
use CMS\Setup\Roles;
use CMS\Setup\Email_Templates;

class Activator {
    public static function activate() {
        // Create database tables
        Tables::create_all();

        // Create caregiver role and capabilities
        Roles::create_caregiver_role();

        // Set up email templates
        Email_Templates::setup();

        // Set version
        add_option('cms_version', CMS_VERSION);

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}