<?php
namespace CMS\Setup;

class Email_Templates {
    public static function setup() {
        add_option('cms_email_welcome', self::get_welcome_template());
        add_option('cms_email_schedule', self::get_schedule_template());
        add_option('cms_email_reminder', self::get_reminder_template());
    }

    private static function get_welcome_template() {
        return array(
            'subject' => __('Welcome to [site_name] - Your Caregiver Account', 'caregiver-management-system'),
            'body' => sprintf(
                __(
                    'Hello [first_name],

Welcome to [site_name]! Your caregiver account has been created.

To get started:
1. Click this link to set your password: [password_link]
2. Log in at: [login_url]
3. Complete your profile and set your availability

Your login details:
Username: [username]

If you have any questions, please contact us.

Best regards,
[site_name] Team',
                    'caregiver-management-system'
                )
            )
        );
    }

    private static function get_schedule_template() {
        return array(
            'subject' => __('Your Schedule for [week_dates]', 'caregiver-management-system'),
            'body' => sprintf(
                __(
                    'Hello [first_name],

Your schedule for [week_dates] has been published:

[schedule_details]

Please log in to your account to view the complete schedule and confirm your shifts.

Best regards,
[site_name] Team',
                    'caregiver-management-system'
                )
            )
        );
    }

    private static function get_reminder_template() {
        return array(
            'subject' => __('Shift Reminder: [shift_date]', 'caregiver-management-system'),
            'body' => sprintf(
                __(
                    'Hello [first_name],

This is a reminder of your upcoming shift:

Date: [shift_date]
Shift: [shift_type]
Time: [shift_time]

Please remember to clock in when you arrive at your location.

Best regards,
[site_name] Team',
                    'caregiver-management-system'
                )
            )
        );
    }
}