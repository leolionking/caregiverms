<?php
namespace CMS\Setup;

class Roles {
    public static function create_caregiver_role() {
        add_role(
            'caregiver',
            __('Caregiver', 'caregiver-management-system'),
            array(
                'read' => true,
                'edit_profile' => true,
                'upload_files' => true,
                'cms_clock_in_out' => true,
                'cms_view_schedule' => true,
                'cms_manage_availability' => true
            )
        );
    }
}