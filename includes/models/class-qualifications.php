<?php
namespace CMS\Models;

class Qualifications {
    public static function get_all_qualifications() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT id,name 
            FROM {$wpdb->prefix}cms_qualifications 
            ORDER BY id"
        );
    }

    public static function get_by_id($id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT id, name 
            FROM {$wpdb->prefix}cms_qualifications 
            WHERE id = %d",
            $id
        ));
    }

    public static function get_by_name($name) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT id, name 
            FROM {$wpdb->prefix}cms_qualifications 
            WHERE name = %s",
            $name
        ));
    }

    public static function add($name) {
        global $wpdb;
        
        return $wpdb->insert(
            $wpdb->prefix . 'cms_qualifications',
            array('name' => $name),
            array('%s')
        );
    }
}